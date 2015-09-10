<?php

/**
 * Class MagentoEditionCheck.
 */
class EditionCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Magento edition: %s", Mage::getEdition()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "edt";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Magento edition";
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