<?php

namespace MonkeyI18n\Parser;

use MonkeyI18n\Param;
use MonkeyI18n\Context;

interface Callback {
	public function __invoke( Param $param, array $values, Context $context );
}
