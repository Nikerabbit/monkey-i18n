<?php

namespace MonkeyI18n\Param;

use MonkeyI18n\Param;

class Number extends Param {
	protected $value;

	public function __construct( $value ) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}
}
