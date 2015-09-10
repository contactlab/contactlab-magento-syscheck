<?php

/**
 * Class RewritesCheck.
 */
class RewritesCheck extends AbstractCheck
{
    /**
     * Do check.
     */
    protected function doCheck()
    {
        $this->log->trace("Check for rewrites");
        $this->_checkRewrites();
        return self::SUCCESS;
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "rewrites";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check magento rewrites";
    }

    /**
     * Need Mage object?
     * @return bool
     */
    public function needMageRun()
    {
        return true;
    }

    /**
     * Check rewrite for all modules.
     */
    private function _checkRewrites()
    {
        foreach (array("Commons", "Subscribers", "Template", "Transactional") as $module) {
            $this->log->trace("Check for rewrites into $module");
            $this->_checkRewritesFor($module);
        }
    }

    /**
     * Check rewrite for the module.
     * @param $module
     */
    private function _checkRewritesFor($module)
    {
        $branch = $this->getEnvironment()->getGitBranch();
        $base = $this->getEnvironment()->getGitBase();
        $url = sprintf("%s/%s/app/code/community/Contactlab/%s/etc/config.xml", $base, $branch, $module);
        $this->log->trace("Retrieve $url");
        $configXml = simplexml_load_file($url);
        $this->_checkRewritesForConfig($configXml);
    }

    private function _checkRewritesForConfig(SimpleXMLElement $configXml)
    {
        $this->log->trace("Check for rewrites from xml");
        $this->_checkRewritesForModels($configXml->global->models);
        $this->_checkRewritesForBlocks($configXml->global->blocks);
    }

    /**
     * @param SimpleXMLElement $models
     */
    private function _checkRewritesForModels(SimpleXMLElement $models)
    {
        $this->log->trace("Check for rewrites for models");
        $rewrites = $this->_getRewrites($models);
        foreach ($rewrites['default'] as $rewrite) {
            $this->_checkRewritesForModel($rewrite);
        }
        foreach ($rewrites['resources'] as $rewrite) {
            $this->_checkRewritesForResource($rewrite);
        }
    }

    /**
     * Check rewrite for blocks.
     * @param SimpleXMLElement $blocks
     */
    private function _checkRewritesForBlocks(SimpleXMLElement $blocks)
    {
        $this->log->trace("Check for rewrites for blocks");
        $rewrites = $this->_getRewrites($blocks);
        foreach ($rewrites['default'] as $rewrite) {
            $this->_checkRewritesForBlock($rewrite);
        }
    }

    /**
     * Get Rewrites.
     * @param $parent
     * @return array
     */
    private function _getRewrites(SimpleXMLElement $parent)
    {
        $rv = array("default" => array(), "resources" => array());
        /** @var SimpleXMLElement $model */
        foreach ($parent->children() as $model) {
            if ($model->rewrite->count()) {
                $module = (string) $model->getName();
                foreach ($model->rewrite->children() as $rewrite) {
                    $class = (string) $rewrite->getName();
                    if (preg_match('|resource$|', $module)) {
                        $m = preg_replace("|_resource$|", "", $module);
                        $rv['resources'][] = "{$m}/{$class}";
                    } else {
                        $rv['default'][] = "{$module}/{$class}";
                    }
                }
            }
        }
        return $rv;
    }

    /**
     * Get rewrite for models.
     * @param $rewrite String
     */
    private function _checkRewritesForModel($rewrite)
    {
        $this->log->trace("Check for rewrites for model $rewrite");
        $modelName = Mage::app()->getConfig()->getModelClassName($rewrite);
        $this->_checkClass("Model", $modelName, $rewrite);
    }

    /**
     * Get rewrite for models resources.
     * @param $rewrite String
     */
    private function _checkRewritesForResource($rewrite)
    {
        $this->log->trace("Check for rewrites for resource model $rewrite");
        $modelName = Mage::app()->getConfig()->getResourceModelClassName($rewrite);
        $this->_checkClass("Resource", $modelName, $rewrite);
    }

    /**
     * Get rewrite for blocks.
     * @param $rewrite String
     */
    private function _checkRewritesForBlock($rewrite)
    {
        $this->log->trace("Check for rewrites for block $rewrite");
        $block = Mage::app()->getConfig()->getBlockClassName($rewrite);
        $this->_checkClass("Block", $block, $rewrite);
    }

    /**
     * Check the class
     * @param $type String
     * @param $className String
     * @param $name String
     */
    private function _checkClass($type, $className, $name)
    {
        if (preg_match('|^Contactlab|', $className)) {
            $this->success("Module installed: $name $type is $className");
        } else if (preg_match('|^Mage_|', $className)) {
            if ($this->_hasLocalFallback($className)) {
                $this->error("Core class has local fallback: $name $type is $className");
            } else if ($this->_hasCommunityFallback($className)) {
                $this->error("Core class has community fallback: $name $type is $className");
            } else {
                $this->success("Core class: $name $type is $className");
            }
        } else {
            $this->error("Rewrite problem: $name $type is $className");
        }
    }

    /**
     * Has Local Fallback?
     * @param $className String
     * @return bool
     */
    private function _hasLocalFallback($className)
    {
        return $this->_hasFallback('local', $className);
    }

    /**
     * Has Community Fallback?
     * @param $className String
     * @return bool
     */
    private function _hasCommunityFallback($className)
    {
        return $this->_hasFallback('community', $className);
    }

    /**
     * Has fallback?
     * @param $pool String
     * @param $className String
     * @return bool
     */
    private function _hasFallback($pool, $className)
    {
        $base = $this->getEnvironment()->getBasePath();
        $filename = sprintf("%s/app/code/%s/%s.php", $base, $pool, str_replace('_', '/', $className));
        return is_file($filename);
    }
}