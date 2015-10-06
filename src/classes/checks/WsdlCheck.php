<?php

/**
 * Class WsdlCheck.
 */
class WsdlCheck extends AbstractCheck
{

    /**
     * Do check.
     */
    protected function doCheck()
    {
        return $this->_getCheckWsdl() ? $this->success(sprintf("Wsdl test ok")) : $this->error(sprintf("Wsdl test error"));
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "wsdl";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Wsdl test";
    }

    /**
     * Need Database?
     * @return bool
     */
    public function needMageRun()
    {
        return true;
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 200;
    }

    /**
     * Check wsdl.
     */
    private function _getCheckWsdl()
    {
        $wsdl = Mage::getStoreConfig("contactlab_commons/soap/wsdl_url");
        $xml = @simplexml_load_file($wsdl);
        if (!$xml) {
            return false;
        }
        new SoapClient($wsdl, array('soap_version' => SOAP_1_2));
        return true;
    }
}