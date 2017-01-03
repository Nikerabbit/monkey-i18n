<?php

namespace MonkeyI18n;

abstract class Param {
	public abstract function getValue();

	public function __toString() {
		return $this->getValue();
	}
}
