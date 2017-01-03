<?php

namespace MonkeyI18n;

class Context {
	protected $language;
	protected $recipient;

	public function __construct( Language $language, User $recipient = null, callable $escaper = null ) {
		$this->language = $language;
		$this->recipient = $recipient;
		$this->escaper = $escaper;
	}

	public function setLanguage( Language $language ) {
		$this->language = $language;
	}

	public function setRecipient( User $recipient ) {
		$this->recipient = $recipient;
	}

	public function setEscaper( callable $escaper ) {
		$this->escaper = $escaper;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function getRecipient() {
		return $this->recipient;
	}

	public function escape( $mixed ) {
		if ( $this->escaper ) {
			return call_user_func( $this->escaper, $mixed );
		}

		return $mixed;
	}
}

#$msg
#	->param( 'user', 'user', $foo )
#	->param( 'text', 'link', $bar );
