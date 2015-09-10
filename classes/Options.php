<?php

class Options
{
    private $checks = array();
    private $onlyList = false;
    private $help = false;
    private $path = false;

    public function __construct($options)
    {
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
     * Get path?
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }
}