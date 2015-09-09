<?php

/**
 * Class SessionCheck.
 */
class SessionCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Session: %s", $this->getCacheBackend()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "session";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Session backend.";
    }

    /**
     * Need Database?
     * @return bool
     */
    public function needMageRun()
    {
        return true;
    }

    /**
     * Get cache backend.
     * @return String
     */
    private function getCacheBackend()
    {
        $config = Mage::app()->getConfig();
        return (string) $config->getNode('global/session_save');
    }
}