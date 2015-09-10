<?php

class MagentoEnvironment
{
    /**
     * @var String
     */
    private $_basePath;

    /**
     * @var mysqli
     */
    private $_mysqli;

    /**
     * Database table prefix.
     * @var String
     */
    private $_dbPrefix;

    /**
     * Options.
     * @var Options
     */
    private $_options;

    /**
     * Get base path.
     * @return String
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Set base path.
     * @param String $basePath
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = $basePath;
    }

    /**
     * Set database connection.
     * @param $mysqli
     */
    public function setDb(mysqli $mysqli)
    {
        $this->_mysqli = $mysqli;
    }

    /**
     * Get database connections.
     * @return mysqli
     */
    public function getDb()
    {
        return $this->_mysqli;
    }

    /**
     * @return String
     */
    public function getDbPrefix()
    {
        return $this->_dbPrefix;
    }

    /**
     * @param String $dbPrefix
     */
    public function setDbPrefix($dbPrefix)
    {
        $this->_dbPrefix = $dbPrefix;
    }

    /**
     * Git branch.
     * @return string
     */
    public function getGitBranch()
    {
        return "develop";
    }

    public function getGitBase()
    {
        return "https://raw.githubusercontent.com/contactlab/contactlab-magento-connect";
    }

    /**
     * Set options.
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->_options = $options;
    }

    /**
     * Get options.
     * @return Options
     */
    public function getOptions()
    {
        return $this->_options;
    }
}