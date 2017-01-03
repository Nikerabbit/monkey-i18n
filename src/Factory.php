<?php

namespace MonkeyI18n;

use MonkeyI18n\Param;
use MonkeyI18n\Param\Quantity;
use MonkeyI18n\Param\User;

class Factory {
	protected $manager;
	protected $context;

	protected $key;
	protected $params = [];

	protected $autoBoxParams = true;
	protected $paramFormat = "$\0";

	public function __construct( Manager $manager, Context $defaultContext ) {
		$this->manager = $manager;
		$this->context = $defaultContext;
	}

	public function __invoke( $key ) {
		$self = clone( $this );

		$self->key = $key;

		$args = func_get_args();
		foreach ( $args as $index => $value ) {
			if ( $index === 0 ) {
				continue;
			}

			$key = strtr( $this->paramFormat, ["\0" => $index] );

			$self->params[$key] = $this->box( $value );
		}

		return $self;
	}

	public function box( $value ) {
		if ( $value instanceof Param ) {
			return $value;
		}

		if ( !$this->autoBoxParams ) {
			return new Text( $value );
		}

		if ( is_int( $value ) ) {
			return new Quantity( $value );
		}

		return new Text( $value );
	}

	public function with( $key, $param ) {
		$key = strtr( $this->paramFormat, ["\0" => $key] );
		$param = $this->box( $param );
		$this->params[$key] = $param;

		return $this;
	}

	public function inLanguage( Language $language ) {
		$this->context->setLanguage( $language );

		return $this;
	}

	public function forRecipient( User $recipient ) {
		$this->context->setRecipient( $recipient );

		return $this;
	}

	public function escapeWith( callable $escaper ) {
		$this->context->setEscaper( $escaper );

		return $this;
	}

	public function __toString() {
		return (string)$this->manager->getTranslation( $this->context, $this->key, $this->params );
	}
}
