<?php

namespace MonkeyI18n;

use MonkeyI18n\Parser\Gender;
use MonkeyI18n\Parser\Plural;
use MonkeyI18n\Parser\Sub;

class Manager {
	protected $messages = [];
	protected $parser;

	protected $fallbacks = [];
	protected $langDB;

	protected $languages;

	protected $defaultFallback = 'en';

	public static function defaultManager() {
		$obj = new self();

		$plurals = json_decode( file_get_contents( __DIR__ . '/../data/plurals.json' ), true );

		$parser = new Parser\CallbackParser();
		$parser->registerCallback( 'PLURAL', new Plural( $plurals['supplemental']['plurals-type-cardinal'] ) );
		$parser->registerCallback( 'GENDER', new Gender() );
		$parser->registerCallback( 'EMBED', new Sub() );
		$obj->parser = $parser;

		$obj->fallbacks = json_decode( file_get_contents( __DIR__ . '/../data/fallbacks.json' ), true );
		$obj->langDB = yaml_parse( file_get_contents( __DIR__ . '/../data/langdb.yaml' ) );

		return $obj;
	}

	public function getMessageFactory() {
		$context = new Context( $this->getLanguage( 'en' ) );

		return new Factory( $this, $context );
	}

	public function getLanguage( $code ) {
		if ( !isset( $this->languages[$code] ) ) {
			$dir = Language::LTR;
			if ( isset( $this->langDB['language'][$code] ) ) {
				$script = $this->langDB['language'][$code];
				if ( in_array( $script, $this->langDB['rtlscripts'], true ) ) {
					$dir = Language::RTL;
				}
			}

			$this->languages[$code] = new Language( $code, $dir );
		}

		return $this->languages[$code];
	}

	public function registerMessages( $language, $messages ) {
		if ( !isset( $this->messages[$language] ) ) {
			$this->messages[$language] = [];
		}

		$this->messages[$language] = array_merge( $this->messages[$language], $messages );
	}

	public function registerMessageDirectory() {

	}

	public function getTranslation( Context $context, $key, array $params ) {
		$chain = $this->getFallbackChain( $context->getLanguage()->getCode() );

		list( $actualLanguage, $content ) = $this->getTranslationWithChain( $chain, $key );

		$parsed = $this->parser->parse( $content, $params, $context );

		return new Message( $key, $parsed, $this->getLanguage( $actualLanguage ) );
	}

	protected function getFallbackChain( $language ) {
		$fallbacks = [$language];
		if ( isset( $this->fallbacks[$language] ) ) {
			$fallbacks += $this->fallbacks[$language];
		}
		$fallbacks[] = $this->defaultFallback;

		return $fallbacks;
	}

	protected function getTranslationWithChain( $languages, $key ) {
		foreach ( $languages as $language ) {
			if ( isset( $this->messages[$language][$key] ) ) {
				return [$language, $this->messages[$language][$key]];
			}
		}

		throw new \RuntimeException( "Unknown key '$key'" );
	}
}
