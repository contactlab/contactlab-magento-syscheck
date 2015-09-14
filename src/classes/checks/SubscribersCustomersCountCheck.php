<?php

/**
 * Class SubscribersCustomerCountCheck
 */
class SubscribersCustomersCountCheck extends AbstractCheck
{
    private $count;

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $this->count = $this->getCustomersCount();
        return $this->success(sprintf("Newsletter Subscribers customers count: %d", $this->count));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "subscribed-customer-count";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Newsletter Subscribers customers count";
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
        $this->log->trace("Check subscribers with Customer count from database");
        $sql = sprintf("select count(1) from %s where customer_id != 0", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 120;
    }

    /**
     * Get log data to send.
     * @return int
     */
    public function getLogData()
    {
        return $this->count;
    }

    /**
     * Do send log data.
     * @return bool
     */
    public function doSendLogData()
    {
        return true;
    }
}