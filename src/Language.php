<?php

namespace MonkeyI18n;

class Language {
	const LTR = 'ltr';
	const RTL = 'rtl';

	public function __construct( $code, $dir ) {
		$this->code = $code;
		$this->dir = $dir;
	}

	public function getCode() {
		return $this->code;
	}

	public function getDir() {
		return $this->dir;
	}

	public function __toString() {
		return $this->code;
	}
}
