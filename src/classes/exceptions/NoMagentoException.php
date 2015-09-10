<?php

/**
 * Class NoMagentoException.
 */
class NoMagentoException extends IllegalStateException
{
    public function __construct()
    {
        parent::__construct("Not a Magento installation");
    }
}