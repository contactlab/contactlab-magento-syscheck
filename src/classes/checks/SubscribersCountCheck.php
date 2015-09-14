<?php

/**
 * Class SubscribersCountCheck
 */
class SubscribersCountCheck extends AbstractCheck
{
    private $count;

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $count = $this->count();
        $this->count = $count;
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
        return "subscribers-count";
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

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 90;
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