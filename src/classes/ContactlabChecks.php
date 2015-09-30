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
     * @var array[CheckInterface]
     */
    private $_checks = array();

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
     * Get mail head file.
     * @return string
     */
    public function getHeadMailFile()
    {
        return realpath(__DIR__ . '/../../etc/header.html');
    }

    /**
     * Get mail footer file.
     * @return string
     */
    public function getFooterMailFile()
    {
        return realpath(__DIR__ . '/../../etc/footer.html');
    }

    /**
     * Start Checks.
     */
    private function _startChecks()
    {
        $this->log->trace("Start checks");
        foreach ($this->getAvailableChecks() as $checkInstance) {
            /** @var CheckInterface $checkInstance */
            $checks = $this->getEnvironment()->getOptions()->getChecks();
            if (!empty($checks) && !in_array($checkInstance->getCode(), $checks)) {
                $this->log->trace("Skip this check (not included in args)");
                continue;
            }
            $this->_checks[] = $checkInstance;
            $this->_startCheck($checkInstance);
        }
        if ($this->_dbConnected) {
            $this->getEnvironment()->getDb()->close();
        }
        if ($this->getOptions()->mustSendMail()) {
            $this->doSendMail();
        }
    }

    /**
     * List Checks.
     */
    private function _listChecks()
    {
        $this->log->trace("List checks");
        /** @var CheckInterface $checkInstance */
        foreach ($this->getAvailableChecks() as $checkInstance) {
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
            $output = sprintf("[%s] #%s %s\n",
                $exitCode,
                strtolower($checkInstance->getCode()),
                $checkInstance->getDescription());
            print($this->getEnvironment()->getColor()->getColoredStringByCode($output, $exitCode));
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
        printf("#%-28s %s\n", strtolower($checkInstance->getCode()), $checkInstance->getDescription());
    }

    /**
     * Run (require) Magento.
     */
    private function _requireMagento()
    {
        $this->log->trace("Requiring magento");
        /** @noinspection PhpIncludeInspection */
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
        /** @noinspection PhpUndefinedFieldInspection */
        $db = $localXml->global->resources->default_setup->connection;
        /** @noinspection PhpUndefinedFieldInspection */
        $prefix = (string) $localXml->global->resources->db->table_prefix;
        $this->getEnvironment()->setDbPrefix($prefix);
        $host = (string) $db->host;
        if ($this->isSocket($host)) {
            $mysqli = new mysqli(NULL,
                (string) $db->username,
                (string) $db->password,
                (string) $db->dbname,
                NULL, $host);
        } else {
            $mysqli = new mysqli($host, (string) $db->username, (string) $db->password, (string) $db->dbname);
        }
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
        /** @noinspection PhpUndefinedClassInspection */
        Mage::app();
    }

    private function _checkContactlabPlugins()
    {
        /** @noinspection PhpUndefinedClassInspection */
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

    /**
     * Send report mail.
     * @throws IllegalStateException
     */
    private function doSendMail()
    {
        $configuration = $this->getConfiguration();
        $mail = $configuration->mail;
        if (empty($mail->report_recipients)) {
            throw new IllegalStateException("No recipients configured");
        }
        if (empty($mail->from)) {
            throw new IllegalStateException("No mail sender configured");
        }
        $header = "From: " . $this->buildMailRecipient($mail->from) . "\n";
        $recipients = $mail->report_recipients;
        if (count($recipients) > 1) {
            $header .= "CC: " . $this->buildMailCCRecipients($recipients) . "\n";
        }
        $header .= "MIME-Version: 1.0\n";
        $header .= "Content-Type: text/html; charset=\"utf8\"\n";
        $header .= "Content-Transfer-Encoding: 7bit\n\n";
        $msg = $this->getMailBody();
        $subject = $this->getMailSubject();
        if (!@mail($this->buildMailRecipient($recipients[0]),
            $subject, $msg, $header)) {
            throw new IllegalStateException("Could not send notification email");
        }
    }

    private function buildMailRecipient($recipient)
    {
        return sprintf("%s <%s>", $recipient->name, $recipient->mail);
    }

    private function buildMailCCRecipients($recipients)
    {
        $rv = "";
        foreach(array_slice($recipients, 1) as $recipient) {
            $rv .= ", " . $this->buildMailRecipient($recipient);
        }
        return preg_replace('|^, |', '', $rv);
    }

    private function getMailBody()
    {
        $success = $this->buildHtmlReport(CheckInterface::SUCCESS);
        $skipped = $this->buildHtmlReport(CheckInterface::SKIP);
        $fatal = $this->buildHtmlReport(CheckInterface::FATAL);
        $error = $this->buildHtmlReport(CheckInterface::ERROR);

        $body = $fatal . $error . $skipped . $success;

        return file_get_contents($this->getFooterMailFile())
             . $body . file_get_contents($this->getFooterMailFile());
    }

    private function getMailSubject()
    {
        $subject = "Contactlab Magento Syscheck";
        $success = $this->countChecks(CheckInterface::SUCCESS);
        $skipped = $this->countChecks(CheckInterface::SKIP);
        $fatal = $this->countChecks(CheckInterface::FATAL);
        $error = $this->countChecks(CheckInterface::ERROR);
        if ($skipped + $fatal + $error === 0 && $success > 0) {
            return sprintf("%s - Success: %s checks", $subject, $success);
        }
        return sprintf("%s - Success: %s, skipped: %s, error: %s, Fatal: %s",
            $subject, $success, $skipped, $error, $fatal);
    }

    /**
     * @param $exitCode
     * @return int
     */
    private function countChecks($exitCode)
    {
        $rv = 0;
        /** @var CheckInterface $check */
        foreach ($this->_checks as $check) {
            if ($check->getExitCode() === $exitCode) {
                $rv++;
            }
        }
        return $rv;
    }

    private function buildHtmlReport($exitCode)
    {
        $rv = "";
        $found = false;
        /** @var CheckInterface $check */
        foreach ($this->_checks as $check) {
            if ($check->getExitCode() === $exitCode) {
                $rv .= "<h5>" . $check->getName() . "</h5>";
                $rv .= $check->toHtml();
                $found = true;
            }
        }
        if (!$found) {
            return "";
        }
        return "<h4>[$exitCode]</h4>" . $rv;
    }

    /**
     * Get Available checks
     * @return array
     */
    private function getAvailableChecks()
    {
        $checkDir = __DIR__ . '/checks';
        $rv = array();
        foreach (scandir($checkDir) as $file) {
            if (!preg_match('|\.php$|', $file)) {
                continue;
            }
            $className = preg_replace('|\.php$|', '', $file);
            $rv[] = new $className();
        }
        usort($rv, function ($a, $b) {
            /**  @var $a CheckInterface */
            /**  @var $b CheckInterface */
            $a = $a->getPosition();
            $b = $b->getPosition();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        return $rv;
    }

    /**
     * Is it a socket connection?
     * @param $host String
     * @return bool
     */
    public static function isSocket($host)
    {
        return is_readable($host);
    }
}
