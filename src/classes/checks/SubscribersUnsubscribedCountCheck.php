<?php

/**
 * Class SubscribersUnsubscribedCountCheck
 */
class SubscribersUnsubscribedCountCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Unsubscribed count: %d", $this->getCustomersCount()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-s3";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Unsubscribed Newsletter subscribers count";
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
        $this->log->trace("Check unsubscribed Customer count from database");
        $sql = sprintf("select count(1) from %s where subscriber_status != 1", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 110;
    }
}