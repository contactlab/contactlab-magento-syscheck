<?php

/**
 * Class InstallDateCheck.
 */
class InstallDateCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Magento installed: %s", $this->getInstallDate()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "id";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check install date";
    }

    /**
     * Need Database?
     * @return bool
     */
    public function needMageRun()
    {
        return true;
    }

    private function getInstallDate()
    {
        $config = Mage::app()->getConfig();
        return $config->getNode('global/install/date');
    }
}