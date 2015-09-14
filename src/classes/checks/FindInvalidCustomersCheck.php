<?php

/**
 * Class FindInvalidCustomersCheck
 */
class FindInvalidCustomersCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->getCount();
        if ($count > 0) {
            return $this->error(sprintf("Invalid customers in newsletter subscribers: %d", $count));
        } else {
            return $this->success("No invalid customers in newsletter subscribers");
        }
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-invalid";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Find invalid customers check";
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
    private function getCount()
    {
        $sql = sprintf("select count(1) from %s where customer_id != 0 and customer_id not in (select entity_id from %s);",
            $this->_getTableName('newsletter_subscriber'),
            $this->_getTableName('customer_entity'));
        return $this->_getSqlResult($sql);
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 140;
    }
}