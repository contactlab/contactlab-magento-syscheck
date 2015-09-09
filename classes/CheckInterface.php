<?php

interface CheckInterface
{
    const SUCCESS = "Ok";
    const SKIP = "Skip";
    const ERROR = "Error";
    const FATAL = "Fatal";

    /**
     * Do Check.
     * @return int
     */
    public function check();

    /**
     * Get name.
     * @return String
     */
    public function getName();

    /**
     * Get code.
     * @return String
     */
    public function getCode();

    /**
     * Get description.
     * @return String
     */
    public function getDescription();

    /**
     * Set environment.
     * @param MagentoEnvironment $_environment
     */
    public function setEnvironment(MagentoEnvironment $_environment);

    /**
     * Need Magento.
     * @return bool
     */
    public function needMage();

    /**
     * Need MagentoRun.
     * @return bool
     */
    public function needMageRun();

    /**
     * Need Database.
     * @return bool
     */
    public function needDatabase();

    /**
     * Report success.
     */
    public function reportSuccess();

    /**
     * Report errors.
     */
    public function reportError();
}