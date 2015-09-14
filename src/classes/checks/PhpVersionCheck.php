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
        return "php-version";
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

    /**
     * Get log data to send.
     * @return int
     */
    public function getLogData()
    {
        return $this->getPhpVersion();
    }

    /**
     * Do send log data.
     * @return bool
     */
    public function doSendLogData()
    {
        return true;
    }
}