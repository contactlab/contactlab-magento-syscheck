<?php

require_once __DIR__ . '/autoload.php';

Logger::configure(array('root'), 'MyLoggerConfigurator');

$shortOpts = "c:lhp:ms";
$longOpts = array("checks:", "list", "help", "path:", "mail", "send");
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
if (isset($options['m'])) {
    $options['mail'] = $options['m'];
    unset($options['m']);
}
if (isset($options['s'])) {
    $options['send'] = $options['s'];
    unset($options['s']);
}
try {
    $checks = new ContactlabChecks(new Options($options));
    $checks->run();
} catch (IllegalStateException $e) {
    $color = new Color();
    echo $color->getColoredString("Exception: " . $e->getMessage(), 'light_red') . "\n";
}

