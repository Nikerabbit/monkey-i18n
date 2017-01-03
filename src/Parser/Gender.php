<?php

namespace MonkeyI18n\Parser;

use MonkeyI18n\Context;
use MonkeyI18n\Param;
use MonkeyI18n\Param\Text;
use MonkeyI18n\Param\User;

class Gender implements Callback {
	public function __invoke( Param $param, array $forms, Context $context ) {
		// No forms given
		if ( !isset( $forms[0] ) ) {
			return '';
		}

		if ( $param instanceof Text && (string)$param === '#' ) {
			$user = $context->getUser();
		} else {
			$user = $param;
		}

		if ( !$param instanceof User ) {
			throw new \RuntimeException( "Gender needs an user" );
		}

		$genderMap = [
			User::MALE => 0,
			User::FEMALE => 1,
			User::OTHER => 2,
		];

		$index = 2;
		$gender = $param->getGender();
		if ( isset( $genderMap[$gender] ) ) {
			$index = $genderMap[$gender];
		}

		if ( isset( $forms[$index] ) ) {
			return $forms[$index];
		} else {
			return $forms[0];
		}
	}
}
