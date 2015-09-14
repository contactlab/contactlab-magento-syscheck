<?php

class Options
{
    private $checks = array();
    private $onlyList = false;
    private $help = false;
    private $path = false;
    private $sendMail = false;
    private $sendData = false;

    /**
     * Logger.
     * @var Logger
     */
    private $log;

    public function __construct($options = false)
    {
        $this->log = Logger::getLogger(__CLASS__);
        if ($options !== false) {
            $this->readOptions($options);
        }
    }


    /**
     * @return array
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * Do only list?
     * @return boolean
     */
    public function isOnlyList()
    {
        return $this->onlyList;
    }

    /**
     * Only print help.
     * @return boolean
     */
    public function isHelp()
    {
        return $this->help;
    }

    /**
     * Has path?
     * @return boolean
     */
    public function hasPath()
    {
        return $this->path !== false;
    }

    /**
     * Do send data?
     * @return boolean
     */
    public function sendData()
    {
        return $this->sendData;
    }

    /**
     * Get path?
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Read options.
     * @param $options
     */
    public function readOptions($options)
    {
        $this->resetOptions();
        if (isset($options['checks'])) {
            if (!is_array($options['checks'])) {
                $options['checks'] = array($options['checks']);
            }
            $this->checks = $options['checks'];
        }
        if (isset($options['list'])) {
            $this->onlyList = true;
        }
        if (isset($options['help'])) {
            $this->help = true;
        }
        if (isset($options['path'])) {
            $this->path = $options['path'];
        }
        if (isset($options['mail'])) {
            $this->sendMail = true;
        }
        if (isset($options['send'])) {
            $this->sendData = true;
        }
    }

    /**
     * Reset options.
     */
    public function resetOptions()
    {
        $this->checks = array();
        $this->onlyList = false;
        $this->help = false;
        $this->path = false;
        $this->sendMail = false;
        $this->sendData = false;
    }

    /**
     * Do send mail?
     * @return bool
     */
    public function mustSendMail()
    {
        return $this->sendMail;
    }
}