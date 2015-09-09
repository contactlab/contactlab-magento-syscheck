<?php

abstract class AbstractCheck implements CheckInterface
{
    private $_environment;
    private $_success = array();
    private $_error = array();

    /**
     * Start check.
     * @return String
     */
    public function check() {
        return $this->doCheck();
    }

    /**
     * Get Name.
     * @return string
     */
    function getName()
    {
        return get_class($this);
    }

    /**
     * Start check.
     * @return String
     */
    protected abstract function doCheck();

    /**
     * Set environment.
     * @param MagentoEnvironment $_environment
     */
    public function setEnvironment(MagentoEnvironment $_environment) {
        $this->_environment = $_environment;
    }

    /**
     * Get environment.
     * @return MagentoEnvironment
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }


    /**
     * Output.
     * @param $value
     */
    protected function outputLine($value) {
        printf("     %s\n", $value);
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function success($value)
    {
        $this->_addSuccess($value);
        return self::SUCCESS;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function error($value)
    {
        $this->_addError($value);
        return self::ERROR;
    }

    /**
     * Need Mage object?
     * @return bool
     */
    public function needMage()
    {
        return false;
    }

    /**
     * Need Mage object?
     * @return bool
     */
    public function needMageRun()
    {
        return false;
    }

    /**
     * Need a database Connection?
     * @return bool
     */
    public function needDatabase()
    {
        return false;
    }

    /**
     * Table name with prefix.
     * @param $string
     * @return string
     */
    protected function _getTableName($string)
    {
        return $this->getEnvironment()->getDbPrefix() . $string;
    }

    /**
     * Get sql result.
     * @param $sql
     * @return int
     * @throws FailedCheckException
     */
    protected function _getSqlResult($sql)
    {
        $stmt = $this->getEnvironment()->getDb()->prepare($sql);
        if (!$stmt) {
            throw new FailedCheckException($this->getEnvironment()->getDb()->error);
        }
        $stmt->bind_result($count);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
        return $count;
    }

    /**
     * @param $value
     */
    private function _addSuccess($value)
    {
        $this->_success[] = $value;
    }

    private function _addError($value)
    {
        $this->_error[] = $value;
    }

    public function reportSuccess() {
        foreach ($this->_success as $line) {
            $this->outputLine("[Ok] " . $line);
        }
    }

    public function reportError() {
        foreach ($this->_error as $line) {
            $this->outputLine("[Error] " . $line);
        }
    }
}