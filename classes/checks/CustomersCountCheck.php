<?php

/**
 * Class CustomersCountCheck.
 */
class CustomersCountCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->getCustomersCount();
        if ($count === 0) {
            return $this->error("No customers found");
        }
        if ($count > 1000000) {
            return $this->error("$count customers found!");
        }
        return $this->success(sprintf("Customer count: %d", $count));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-c";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check customers count.";
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
        $sql = sprintf("select count(1) from %s", $this->_getTableName('customer_entity'));
        return $this->_getSqlResult($sql);
    }
}