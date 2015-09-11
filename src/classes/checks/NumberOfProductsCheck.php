<?php

/**
 * Class NumberOfProductsCheck
 */
class NumberOfProductsCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->count();
        if ($count === 0) {
            return $this->error("No products found!");
        }
        if ($count > 1000000) {
            return $this->error("$count products found!");
        }
        return $this->success(sprintf("Products count: %d", $count));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "products";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check products count";
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
        $sql = sprintf("select count(1) from %s", $this->_getTableName('catalog_product_entity'));
        return $this->_getSqlResult($sql);
    }
}