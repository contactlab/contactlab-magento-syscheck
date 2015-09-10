<?php

/**
 * Class SubscribersCountCheck
 */
class SubscribersCountCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->count();
        if ($count === 0) {
            return $this->error("No newsletter subscribers found!");
        }
        if ($count > 1000000) {
            return $this->error("$count newsletter subscribers found!");
        }
        return $this->success(sprintf("Subscribers count: %d", $count));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "cnt-s1";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check Newsletter subscribers count";
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
        $this->log->trace("Check newsletter subscribers from database");
        $sql = sprintf("select count(1) from %s", $this->_getTableName('newsletter_subscriber'));
        return $this->_getSqlResult($sql);
    }
}