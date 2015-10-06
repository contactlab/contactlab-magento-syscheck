<?php

/**
 * Class BaseUrlCheck.
 */
class BaseUrlCheck extends AbstractCheck
{
    private $urls;

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $urls = $this->getBaseUrls();
        $this->urls = $urls;
        if (empty($urls)) {
            return $this->error("No base urls configured");
        }
        foreach ($this->urls as $url) {
            $this->success(sprintf("Base url: %s", $url));
        }
        return self::SUCCESS;
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "base-urls";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check base urls";
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
     * @return string
     * @throws FailedCheckException
     */
    private function getBaseUrls()
    {
        $sql = sprintf("select distinct value from %s where path in ('%s', '%s')",
            $this->_getTableName('core_config_data'), 'web/unsecure/base_url', 'web/secure/base_url');
        $this->log->debug("Sql: " . $sql);
        $stmt = $this->getEnvironment()->getDb()->prepare($sql);
        if (!$stmt) {
            throw new FailedCheckException($this->getEnvironment()->getDb()->error);
        }
        $stmt->bind_result($url);
        $stmt->execute();
        $rv = array();
        while ($stmt->fetch()) {
            $rv[] = $url;
        }
        $stmt->close();
        return $rv;
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 35;
    }
}
