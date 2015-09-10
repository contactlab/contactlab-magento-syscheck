<?php

/**
 * Class MemoryLimitCheck.
 */
class MemoryLimitCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $native = $this->getMemoryLimit();
        $mage = $this->getMageMemoryLimit();
        $error = false;
        if ($native == -1) {
            $this->success(sprintf("No native Memory Limit"));
        } else if ($native < 500000) {
            $this->error(sprintf("Native Memory Limit: %s", $native));
            $error = true;
        } else {
            $this->success(sprintf("Native Memory Limit: %s", $native));
        }
        if (empty($mage)) {
            $this->error(sprintf("No configured Memory Limit"));
            $error = true;
        } else {
            $this->success(sprintf("Configured Memory Limit: %s", $mage));
        }
        return $error ? self::ERROR : self::SUCCESS;
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "mem";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Php Memory Limit";
    }

    public function needMageRun()
    {
        return true;
    }

    private function getMemoryLimit()
    {
        return ini_get('memory_limit');
    }

    private function getMageMemoryLimit()
    {
        Mage::getStoreConfig("contactlab_subscribers/global/memory_limit");
    }

}