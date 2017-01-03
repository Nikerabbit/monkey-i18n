<?php

namespace MonkeyI18n\Parser;

#use MonkeyI18n\Parser;
use MonkeyI18n\Context;
use MonkeyI18n\Param\Text;
use MonkeyI18n\Param\User;
use MonkeyI18n\Param\Unescaped;

class CallbackParser {
	const START = '{{';
	const NAME_SEP = ':';
	const PARAM_SEP = '|';
	const END = '}}';

	protected $callbacks;

	public function registerCallback( $name, callable $callback ) {
		$this->callbacks[$name] = $callback;
	}

	public function parse( $content, array $params, Context $context ) {
		if ( count( $this->callbacks ) === 0 ) {
			return $content;
		}

		$offset = 0;
		$len = strlen( $content );
		$stack = [];
		$state = [];
		$depth = -1;

		$preRep = [];
		$postRep = [];

		$patterns = [ self::START, self::NAME_SEP, self::PARAM_SEP, self::END ];
		foreach ( $patterns as &$pattern ) {
			$pattern = preg_quote( $pattern, '/' );
		}

		$regexp = '/' . implode( $patterns, '|' ) . '/';


		while ( $offset < $len ) {
			$match = [];
			$ok = preg_match( $regexp, $content, $match, PREG_OFFSET_CAPTURE, $offset );

			if ( $ok === false ) {
				throw new \RuntimeException( 'Parser regexp failed' );
			}

			if ( $ok === 0 ) {
				break;
			}

			list( $token, $pos ) = $match[0];
			$offset = $pos + strlen( $token );
			$_state = isset( $state[$depth] ) ? $state[$depth] : 'outside';

			switch ( $token ) {
				case self::START:
					$depth++;
					$state[$depth] = 'start';
					$stack[$depth] = [
						'start' => $pos,
						'pos' => $pos + strlen( $token ),
						'name' => '',
						'params' => [],
					];
					break;

				case self::PARAM_SEP:
					if ( $_state === 'start' ) {
						// {{FOO|bar}}
						$stack[$depth]['name'] = $this->getToken( $stack[$depth], $pos, $content );
					} elseif ( $_state === 'name' || $_state === 'param' ) {
						$stack[$depth]['params'][] = $this->getToken( $stack[$depth], $pos, $content );
					}

					$stack[$depth]['pos'] = $pos + strlen( $token );
					$state[$depth] = 'param';
					break;

				case self::NAME_SEP:
					if ( $_state === 'start' ) {
						$stack[$depth]['name'] = $this->getToken( $stack[$depth], $pos, $content );
						$stack[$depth]['pos'] = $pos + strlen( $token );
						$state[$depth] = 'name';
					}
					break;

				case self::END:
					if ( $_state === 'outside' ) {
						break;
					}

					if ( $_state === 'name' || $_state === 'param' ) {
						$stack[$depth]['params'][] = $this->getToken( $stack[$depth], $pos, $content );
					}

					$name = $stack[$depth]['name'];
					if ( isset( $this->callbacks[$name] ) && count( $stack[$depth]['params'] ) > 0 ) {
						$primary = array_shift( $stack[$depth]['params'] );
						if ( isset( $params[$primary] ) ) {
							$primary = $params[$primary];
						} else {
							$primary = new Text( $primary );
						}

						$replacement = $this->callbacks[$name]( $primary, $stack[$depth]['params'], $context );
						$start = $stack[$depth]['start'];
						$end = $pos + strlen( $token );

						$lenDiff = strlen( $replacement ) - $end;

						$content = substr_replace( $content, $replacement, $start, $end - $start );

						if ( $replacement instanceof Unescaped ) {
							$postRep[$replacement->getValue()] = $replacement->getRealContent();
						}

						$offset += $lenDiff;
						$len += $lenDiff;
					}

					unset( $stack[$depth], $state[$depth] );
					$depth--;
					break;

				default:
					throw new \RuntimeException( "Invalid token" );
			}
		}

		foreach ( $params as $key => $param ) {
			$preRep[$key] = $param->getValue();
			if ( $param instanceof Unescaped ) {
				$postRep[$param->getValue()] = $param->getRealContent();
			}
		}

		$content = strtr( $content, $preRep );
		$content = $context->escape( $content );
		$content = strtr( $content, $postRep );

		return $content;
	}

	protected function getToken( $stack, $pos, $content ) {
		$start = $stack['pos'];

		return substr( $content, $start, $pos - $start );
	}
}

## 'Please {{SUB:$link|log in}}'

