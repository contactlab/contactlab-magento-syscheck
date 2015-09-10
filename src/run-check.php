<?php

require_once __DIR__ . '/autoload.php';

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
    $checks = new ContactlabChecks(new Options($options));
    $checks->run();
} catch (IllegalStateException $e) {
    echo $e->getMessage() . "\n";
}

