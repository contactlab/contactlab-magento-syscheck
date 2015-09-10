<?php

/**
 * Class ContactlabAuthApiKeyCheck.
 */
class ContactlabAuthApiKeyCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        $this->log->trace("Check contactlab_template/queue/auth_api_key from configuration");
        $key = Mage::getStoreConfig('contactlab_template/queue/auth_api_key');
        if (empty($key)) {
            return $this->error("No auth_api_key specified");
        } else {
            return $this->success("auth_api_key specified");
        }
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "auth-api-key";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check auth_api_key";
    }

    /**
     * Need Contactlab?
     * @return bool
     */
    public function needContactlab()
    {
        return true;
    }
}