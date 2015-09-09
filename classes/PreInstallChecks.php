<?php

/**
 * Class PreInstallChecks.
 */
class PreInstallChecks
{
    /**
     * @var stdClass
     */
    private $_configuration;

    /**
     * @var MagentoEnvironment
     */
    private $_environment;

    /**
     * @var bool
     */
    private $_magentoRequired;

    /**
     * @var bool
     */
    private $_dbConnected;

    /**
     * @var String
     */
    private $_magePath;

    /**
     * @var bool
     */
    private $_magentoRun;

    /**
     * Logger.
     * @var Logger
     */
    private $log;

    /**
     * Construct Checks
     * @param array $args
     * @throws NoMagentoException
     */
    public function __construct(array $args)
    {
        $this->log = Logger::getLogger(__CLASS__);
        $this->log->error("We have liftoff.");
        if (count($args) > 0) {
            $args = array_slice($args, 1);
        }
        $this->_readConfiguration();
        $this->_startChecks($args);
    }

    /**
     * Read configuration.
     */
    private function _readConfiguration()
    {
        $this->_environment = new MagentoEnvironment();
        if (!($this->_magePath = $this->_findMagePath())) {
            throw new NoMagentoException();
        }
        $this->_environment->setBasePath($this->_magePath);
        $this->_configuration = json_decode(file_get_contents($this->_getConfigFile()));
    }

    /**
     * Get configuration file.
     * @return string
     */
    private function _getConfigFile()
    {
        return realpath(__DIR__ . '/../etc/config.json');
    }

    /**
     * Start Checks.
     * @param array $checks
     */
    private function _startChecks(array $checks)
    {
        foreach ($this->_configuration->checks as $check) {
            $checkClass = $check . 'Check';
            /** @var CheckInterface $checkInstance */
            $checkInstance = new $checkClass();
            if (!empty($checks) && !in_array($checkInstance->getCode(), $checks)) {
                continue;
            }
            $this->_startCheck($checkInstance);
        }
        if ($this->_dbConnected) {
            $this->_environment->getDb()->close();
        }
    }

    /**
     * Run single check.
     * @param CheckInterface $checkInstance
     */
    private function _startCheck(CheckInterface $checkInstance)
    {
        $checkInstance->setEnvironment($this->_environment);

        if (($checkInstance->needMageRun() || $checkInstance->needMage()) && !$this->_magentoRequired) {
            $this->_requireMagento();
        }
        if ($checkInstance->needMageRun() && !$this->_magentoRun) {
            $this->_runMagento();
        }
        if ($checkInstance->needDatabase() && !$this->_dbConnected) {
            if (!$this->_magentoRequired) {
                $this->_requireMagento();
            }
            $this->_connectToDb();
        }
        try {
            $exitCode = $checkInstance->check();
            printf("[%s] #%s %s\n",
                $exitCode,
                strtolower($checkInstance->getCode()),
                $checkInstance->getDescription());
            $checkInstance->reportSuccess();
            $checkInstance->reportError();
            echo PHP_EOL;
        } catch (SkipCheckException $e) {
            printf("  Skipped: %s\n", $e->getMessage());
        } catch (FailedCheckException $e) {
            printf("[%s] #%s %s\n",
                CheckInterface::FATAL,
                strtolower($checkInstance->getCode()),
                $checkInstance->getDescription());
            printf("  Failed: %s\n", $e->getMessage());
        }
    }

    /**
     * Run (require) Magento.
     */
    private function _requireMagento()
    {
        require_once($this->_magePath . '/app/Mage.php');
        $this->_magentoRequired = true;
    }

    /**
     * Find Mage Path.
     * @return bool
     */
    private function _findMagePath()
    {
        return $this->_findMagePathInto(getcwd());
    }

    /**
     * Find Mage Path into $dir.
     * @param String $dir
     * @return bool
     */
    private function _findMagePathInto($dir)
    {
        if ($this->_isRoot($dir)) {
            return false;
        } else if ($this->_isMageDir($dir)) {
            return $dir;
        } else {
            return $this->_findMagePathInto(dirname($dir));
        }
    }

    /**
     * Is this the root?
     * @param $dir
     * @return bool
     */
    private function _isRoot($dir)
    {
        return realpath($dir) === '/';
    }

    /**
     * Is Mage dir?
     * @param $dir
     * @return bool
     */
    private function _isMageDir($dir)
    {
        return is_file($dir . '/app/Mage.php');
    }

    /**
     * Connect to database.
     */
    private function _connectToDb()
    {
        $localXml = simplexml_load_file($this->_getLocalXml());
        $db = $localXml->global->resources->default_setup->connection;
        $prefix = (string) $localXml->global->resources->db->table_prefix;
        $this->_environment->setDbPrefix($prefix);
        $mysqli = new mysqli((string) $db->host, (string) $db->username, (string) $db->password, (string) $db->dbname);
        if ($mysqli->connect_errno) {
            throw new IllegalStateException($mysqli->connect_error);
        }
        $this->_environment->setDb($mysqli);
    }

    /**
     * Get local xml.
     * @return string
     */
    private function _getLocalXml()
    {
        return $this->_environment->getBasePath() . '/app/etc/local.xml';
    }

    /**
     * Run Magento app.
     */
    private function _runMagento()
    {
        Mage::app();
    }
}