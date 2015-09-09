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
}