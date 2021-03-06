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
        return $this->success(sprintf("Magento edition: %s", $this->getEdition()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "mage-edition";
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

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 30;
    }

    private function getEdition()
    {
        return Mage::getEdition();
    }
}
