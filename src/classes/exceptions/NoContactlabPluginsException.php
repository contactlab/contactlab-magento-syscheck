<?php

class NoContactlabPluginsException extends SkipCheckException
{
    public function __construct()
    {
        parent::__construct("No Contactlab Plugin installed");
    }
}