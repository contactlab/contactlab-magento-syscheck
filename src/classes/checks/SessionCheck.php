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
        return "Session backend";
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
        $this->log->trace("Check session save backend");
        $config = Mage::app()->getConfig();
        return (string) $config->getNode('global/session_save');
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 60;
    }
}