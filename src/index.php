<?php

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'MonkeyI18n\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'CLDRPluralRuleParser\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/../../CLDRPluralRuleParser/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

$manager = MonkeyI18n\Manager::defaultManager();
$manager->registerMessages( 'en', [
	'hi' => 'Hello {{PLURAL:$1|world|worlds}}',
	'ho' => 'Hello {{EMBED:$n|&amp;}} &nbsp;',
] );

$manager->registerMessages( 'fi', [
	'hi' => 'Hei {{PLURAL:$1|maalima|maalimat}} ja {{PLURAL:$2|kuu|kuut}}',
] );

$factory = $manager->getMessageFactory();

echo $factory( 'hi', 2, 1 )
	->inLanguage( $manager->getLanguage( 'fi' ) ) . "\n";

echo $factory( 'ho' )
	->escapeWith( 'htmlspecialchars' )
	->with( 'n', new MonkeyI18n\Param\Unescaped( "<tag>$1</tag>" ) ) . "\n";
