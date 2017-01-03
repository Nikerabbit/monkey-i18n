<?php

namespace MonkeyI18n\Parser;

use MonkeyI18n\Parser;
use MonkeyI18n\Param\User;

class NullParser implements Parser {
	public function parse( $language, array $params, $content, User $user ) {
		return $content;
	}
}
