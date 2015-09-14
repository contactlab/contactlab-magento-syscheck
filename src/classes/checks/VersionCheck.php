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
        return $this->success(sprintf("Magento version: %s", Mage::getVersion()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "ver";
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
}