<?php

namespace MonkeyI18n\Parser;

use CLDRPluralRuleParser\Evaluator;
use MonkeyI18n\Context;
use MonkeyI18n\Param;
use MonkeyI18n\Param\Quantity;

class Plural implements Callback {
	protected $rules;

	public function __construct( $rules ) {
		$this->rules = $rules;
	}

	public function __invoke( Param $param, array $forms, Context $context ) {
		if ( !$param instanceof Quantity ) {
			$type = get_class( $param );
			throw new \RuntimeException( "Plural callback expects number, got $type." );
		}


		// No forms given
		if ( !isset( $forms[0] ) ) {
			return '';
		}

		$i = $param->getValue();

		$code = $context->getLanguage()->getCode();
		if ( !isset( $this->rules[$code] ) ) {
			// Unsupported language, default to English
			$code = 'en';
		}

		$rules = array_values( $this->rules[$code] );
		array_pop( $rules );

		$index = Evaluator::evaluate( $i, $rules );

		$index = min( $index, count( $forms ) - 1 );
		return $forms[$index];
	}
}
