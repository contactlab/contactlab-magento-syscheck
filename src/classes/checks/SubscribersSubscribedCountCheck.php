<?php

/**
 * Class SubscribersSubscribedCountCheck.
 */
class SubscribersSubscribedCountCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Subscribers count: %d", $this->getCustomersCount()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-s2";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Subscribed Newsletter subscribers count";
    }

    /**
     * Need Database?
     * @return bool
     */
    public function needDatabase()
    {
        return true;
    }

    /**
     * Get customer count.
     * @return int
     */
    private function getCustomersCount()
    {
        $this->log->trace("Check subscribed Customer count from database");
        $sql = sprintf("select count(1) from %s where subscriber_status = 1", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }
}