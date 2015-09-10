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

$shortOpts = "c:lhp:";
$longOpts = array("checks:", "list", "help", "path:");
$options = getopt($shortOpts, $longOpts);
if (isset($options['c'])) {
    $options['checks'] = $options['c'];
    unset($options['c']);
}
if (isset($options['h'])) {
    $options['help'] = $options['h'];
    unset($options['h']);
}
if (isset($options['l'])) {
    $options['list'] = $options['l'];
    unset($options['l']);
}
if (isset($options['p'])) {
    $options['path'] = $options['p'];
    unset($options['p']);
}
try {
    /** @var Options $options */
    new ContactlabChecks(new Options($options));
} catch (IllegalStateException $e) {
    echo $e->getMessage() . "\n";
}
