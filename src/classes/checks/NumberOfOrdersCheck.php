<?php

/**
 * Class NumberOfOrdersCheck
 */
class NumberOfOrdersCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->count();
        if ($count === 0) {
            return $this->error("No orders found!");
        }
        if ($count > 1000000) {
            return $this->error("$count orders found!");
        }
        return $this->success(sprintf("Orders count: %d", $count));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "sales";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check orders count";
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
    private function count()
    {
        $this->log->trace("Check number of orders from database");
        $sql = sprintf("select count(1) from %s", $this->_getTableName('sales_flat_order'));
        return $this->_getSqlResult($sql);
    }
}