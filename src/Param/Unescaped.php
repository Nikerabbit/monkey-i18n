<?php

namespace MonkeyI18n\Param;

use MonkeyI18n\Param;

class Unescaped extends Param {
	protected $value;
	protected $placeholder;

	public function __construct( $value ) {
		$this->value = $value;
		$this->placeholder = $this->random();
	}

	public function getValue() {
		return $this->placeholder;
	}

	public function getRealContent() {
		return $this->value;
	}

	function random() {
		$str = '';
		for ( $n = 0; $n < 35; $n += 7 ) {
			$str .= sprintf( '%07x', mt_rand() & 0xfffffff );
		}

		return $str;
	}
}
