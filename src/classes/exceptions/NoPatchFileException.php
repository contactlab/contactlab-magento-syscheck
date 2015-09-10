<?php

/**
 * Class NoPatchFileException.
 */
class NoPatchFileException extends SkipCheckException
{
    public function __construct()
    {
        parent::__construct("No app/etc/applied.patches.list file found");
    }
}