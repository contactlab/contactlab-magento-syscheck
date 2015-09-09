<?php

/**
 * Class CacheBackendCheck.
 */
class CacheBackendCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Cache backend: %s", $this->getCacheBackend()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cache";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check cache backend.";
    }

    /**
     * Need Database?
     * @return bool
     */
    public function needMageRun()
    {
        return true;
    }

    private function getCacheBackend()
    {
        return get_class(Mage::app()->getCache()->getBackend());
    }
}