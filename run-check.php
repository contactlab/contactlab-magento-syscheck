<?php

function autoLoader($class) {
    if (preg_match('|Exception$|', $class)) {
        return require_once(__DIR__ . '/classes/exceptions/' . $class . '.php');
    } else if (preg_match('|Check$|', $class) && !preg_match('|^Abstract|', $class)) {
        return require_once(__DIR__ . '/classes/checks/' . $class . '.php');
    } else {
        $file = __DIR__ . '/classes/' . $class . '.php';
        if (is_file($file)) {
            return require_once($file);
        }
    }
    return false;
}

require __DIR__ . '/vendor/autoload.php';
spl_autoload_register('autoLoader');

Logger::configure(array('root'), 'MyLoggerConfigurator');

try {
    new ContactlabChecks($argv);
} catch (IllegalStateException $e) {
    echo $e->getMessage() . "\n";
}

