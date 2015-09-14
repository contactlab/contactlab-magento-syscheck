<?php

/**
 * Class PhpVersion.
 */
class PhpVersionCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Php version: %s", $this->getPhpVersion()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "phpv";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Php Version";
    }

    /**
     * Get PHP Version.
     */
    private function getPhpVersion()
    {
        return PHP_VERSION;
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 10;
    }
}