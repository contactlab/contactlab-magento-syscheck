<?php

/**
 * Class PreInstallChecks.
 */
class ContactlabChecks
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
     * Options.
     * @var Options
     */
    private $options;

    /**
     * Construct Checks
     * @param Options $options
     * @throws NoMagentoException
     */
    public function __construct(Options $options)
    {
        $this->log = Logger::getLogger(__CLASS__);
        $this->options = $options;
    }

    /**
     * Run tests.
     * @throws NoMagentoException
     */
    public function run()
    {
        if ($this->options->isHelp()) {
            $this->printHelp();
            return;
        }
        $this->readConfiguration();
        if ($this->options->isOnlyList()) {
            $this->_listChecks();
        } else {
            $this->_startChecks();
        }
    }

    /**
     * Read configuration.
     * @throws NoMagentoException
     */
    public function readConfiguration()
    {
        $this->log->trace("Read configuration");
        $this->_environment = new MagentoEnvironment();
        if (!$this->options->isOnlyList()) {
            if (!($this->_magePath = $this->_findMagePath())) {
                throw new NoMagentoException();
            }
        }
        $this->getEnvironment()->setBasePath($this->_magePath);
        $this->getEnvironment()->setOptions($this->options);
        $this->_configuration = json_decode(file_get_contents($this->getConfigFile()));
    }

    /**
     * Get configuration file.
     * @return string
     */
    public function getConfigFile()
    {
        return realpath(__DIR__ . '/../../etc/config.json');
    }

    /**
     * Start Checks.
     */
    private function _startChecks()
    {
        $this->log->trace("Start checks");
        foreach ($this->getConfiguration()->checks as $check) {
            $checkClass = $check . 'Check';
            /** @var CheckInterface $checkInstance */
            $checkInstance = new $checkClass();
            $checks = $this->getEnvironment()->getOptions()->getChecks();
            if (!empty($checks) && !in_array($checkInstance->getCode(), $checks)) {
                $this->log->trace("Skip this check (not included in args)");
                continue;
            }
            $this->_startCheck($checkInstance);
        }
        if ($this->_dbConnected) {
            $this->getEnvironment()->getDb()->close();
        }
    }

    /**
     * List Checks.
     */
    private function _listChecks()
    {
        $this->log->trace("List checks");
        foreach ($this->getConfiguration()->checks as $check) {
            $checkClass = $check . 'Check';
            /** @var CheckInterface $checkInstance */
            $checkInstance = new $checkClass();
            $checks = $this->getEnvironment()->getOptions()->getChecks();
            if (!empty($checks) && !in_array($checkInstance->getCode(), $checks)) {
                continue;
            }
            $this->_printCheck($checkInstance);
        }
    }

    /**
     * Run single check.
     * @param CheckInterface $checkInstance
     */
    private function _startCheck(CheckInterface $checkInstance)
    {
        $this->log->trace("Starts single check");
        $checkInstance->setEnvironment($this->getEnvironment());

        if (($checkInstance->needContactlab() || $checkInstance->needMageRun() || $checkInstance->needMage()) && !$this->_magentoRequired) {
            $this->_requireMagento();
        }
        if (($checkInstance->needContactlab() || $checkInstance->needMageRun()) && !$this->_magentoRun) {
            $this->_runMagento();
        }
        if ($checkInstance->needDatabase() && !$this->_dbConnected) {
            if (!$this->_magentoRequired) {
                $this->_requireMagento();
            }
            $this->_connectToDb();
        }
        try {
            if ($checkInstance->needContactlab()) {
                if (!$this->_checkContactlabPlugins()) {
                    $this->log->error("Not a Contactlab Installation");
                    throw new NoContactlabPluginsException();
                }
            }
            $exitCode = $checkInstance->check();
            printf("[%s] #%s %s\n",
                $exitCode,
                strtolower($checkInstance->getCode()),
                $checkInstance->getDescription());
            $checkInstance->reportSuccess();
            $checkInstance->reportError();
            echo PHP_EOL;
        } catch (SkipCheckException $e) {
            printf("[%s] #%s %s\n",
                CheckInterface::SKIP,
                strtolower($checkInstance->getCode()),
                $checkInstance->getDescription());
            printf("  Skipped: %s\n", $e->getMessage());
            $this->log->info("Check skipped", $e);
        } catch (FailedCheckException $e) {
            printf("[%s] #%s %s\n",
                CheckInterface::FATAL,
                strtolower($checkInstance->getCode()),
                $checkInstance->getDescription());
            printf("  Failed: %s\n", $e->getMessage());
            $this->log->error("Check failed", $e);
        }
    }

    /**
     * Print single check.
     * @param CheckInterface $checkInstance
     */
    private function _printCheck(CheckInterface $checkInstance)
    {
        printf("#%-15s %s\n", strtolower($checkInstance->getCode()), $checkInstance->getDescription());
    }

    /**
     * Run (require) Magento.
     */
    private function _requireMagento()
    {
        $this->log->trace("Requiring magento");
        require_once($this->_magePath . '/app/Mage.php');
        $this->_magentoRequired = true;
    }

    /**
     * Find Mage Path.
     * @return bool
     */
    private function _findMagePath()
    {
        $this->log->trace("Looking for magento path");
        if ($this->options->hasPath()) {
            $path = $this->options->getPath();
            return $this->_findMagePathInto($path);
        } else {
            return $this->_findMagePathInto(getcwd());
        }
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
        $this->log->trace("Connect to db");
        $localXml = simplexml_load_file($this->_getLocalXml());
        $db = $localXml->global->resources->default_setup->connection;
        $prefix = (string) $localXml->global->resources->db->table_prefix;
        $this->getEnvironment()->setDbPrefix($prefix);
        $mysqli = new mysqli((string) $db->host, (string) $db->username, (string) $db->password, (string) $db->dbname);
        if ($mysqli->connect_errno) {
            throw new IllegalStateException($mysqli->connect_error);
        }
        $this->getEnvironment()->setDb($mysqli);
    }

    /**
     * Get local xml.
     * @return string
     */
    private function _getLocalXml()
    {
        return $this->getEnvironment()->getBasePath() . '/app/etc/local.xml';
    }

    /**
     * Run Magento app.
     */
    private function _runMagento()
    {
        $this->log->trace("Running magento");
        Mage::app();
    }

    private function _checkContactlabPlugins()
    {
        return Mage::helper('core')->isModuleEnabled('Contactlab_Commons');
    }

    /**
     * Print help.
     */
    public function printHelp()
    {
        readfile($this->getHelpFile());
    }

    /**
     * Get help file.
     * @return string
     */
    public function getHelpFile()
    {
        return realpath(__DIR__ . '/../../etc/help.txt');
    }

    /**
     * Options.
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get Environment.
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Get configuration.
     * @return stdClass
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }
}
