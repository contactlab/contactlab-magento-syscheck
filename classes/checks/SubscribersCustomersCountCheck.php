<?php

/**
 * Class SubscribersCustomerCountCheck
 */
class SubscribersCustomersCountCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->success(sprintf("Newsletter Subscribers customers count: %d", $this->getCustomersCount()));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-sc";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Newsletter Subscribers customers count.";
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
        $sql = sprintf("select count(1) from %s where customer_id != 0", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }
}