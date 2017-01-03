<?php

namespace MonkeyI18n;

class Message {
	protected $key;
	protected $content;
	protected $language;

	public function __construct( $key, $content, Language $language ) {
		$this->key = $key;
		$this->content = $content;
		$this->language = $language;
	}

	public function __toString() {
		return $this->content;
	}

	public function getKey() {
		return $this->key;
	}

	public function getLanguage() {
		return $this->language;
	}
}
