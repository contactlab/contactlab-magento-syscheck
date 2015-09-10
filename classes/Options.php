<?php

class Options
{
    private $checks = array();
    private $onlyList = false;

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
}