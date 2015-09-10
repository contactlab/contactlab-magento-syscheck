<?php

/**
 * Class FindDuplicatedCustomersCheck
 */
class FindDuplicatedCustomersCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->getCount();
        if ($count > 0) {
            return $this->error(sprintf("Duplicated customers in newsletter subscribers: %d", $count));
        } else {
            return $this->success("No duplicated customers in newsletter subscribers");
        }
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-dc";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Find duplicated customers check";
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
        $sql = sprintf("select count(1) from (select customer_id, count(1) from %s where customer_id != 0 group by customer_id having count(1) > 1) t;", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }
}