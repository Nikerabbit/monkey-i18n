<?php

namespace MonkeyI18n\Param;

use MonkeyI18n\Param;

class User extends Param {
	const MALE = 'male';
	const FEMALE = 'female';
	const OTHER = 'other';

	protected $name;
	protected $gender;

	public function __construct( $name, $gender ) {
		$this->name = $name;
		$this->gender = $gender;
	}

	public function getValue() {
		return $this->name;
	}

	public function getGender() {
		return $this->gender;
	}
}
