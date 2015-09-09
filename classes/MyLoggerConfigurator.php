<?php

class MyLoggerConfigurator implements LoggerConfigurator
{
    public function configure(LoggerHierarchy $hierarchy, $input = null)
    {
        $pattern = new LoggerLayoutPattern('root');
        $pattern->setConversionPattern("%date [%logger] %message%newline");
        $pattern->activateOptions();

        $appFile = new LoggerAppenderFile();
        $appFile->setFile(__DIR__ . "/../var/log/checks.log");
        $appFile->setAppend(true);
        $appFile->setThreshold('all');
        $appFile->activateOptions();
        $appFile->setLayout($pattern);


        $root = $hierarchy->getRootLogger();
        $root->addAppender($appFile);
        //$root->addAppender($appEcho);
    }
}