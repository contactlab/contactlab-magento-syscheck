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
        return "install-date";
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
        $this->log->trace("Check install date from global/install/date");
        $config = Mage::app()->getConfig();
        return $config->getNode('global/install/date');
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 40;
    }
}