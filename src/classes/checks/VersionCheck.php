<?php

/**
 * Class MagentoVersionCheck.
 */
class VersionCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $this->log->trace("Check Magento version from Mage");
        return $this->success(sprintf("Magento version: %s", $this->_getVersion()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "mage-ver";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Magento version";
    }

    /**
     * Need Mage object?
     * @return bool
     */
    public function needMage()
    {
        return true;
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 20;
    }

    /**
     * Get log data to send.
     * @return int
     */
    public function getLogData()
    {
        return $this->_getVersion();
    }

    /**
     * Do send log data.
     * @return bool
     */
    public function doSendLogData()
    {
        return true;
    }

    /**
     * Get Magento Version.
     * @return String
     */
    private function _getVersion()
    {
        return Mage::getVersion();
    }
}