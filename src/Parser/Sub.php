<?php

namespace MonkeyI18n\Parser;

use MonkeyI18n\Context;
use MonkeyI18n\Param;
use MonkeyI18n\Param\Unescaped;

class Sub implements Callback {
	public function __invoke( Param $param, array $params, Context $context ) {
		if ( !$param instanceof Unescaped ) {
			throw new \RuntimeException( "Sub expects Unescaped param" );
		}

		$content = $param->getRealContent();

		$keys = $values = [];
		foreach ( $params as $index => $param ) {
			$keys[] = $index + 1;
			$values[] = $context->escape( $param );
		}

		$content = strtr( $content, array_combine( $keys, $values ) );

		return new Unescaped( $content );
	}
}
