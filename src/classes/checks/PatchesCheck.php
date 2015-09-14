<?php

/**
 * Class MagentoPatchesCheck.
 */
class PatchesCheck extends AbstractCheck
{

    /**
     * Do Check.
     * @throws NoPatchFileException
     */
    protected function doCheck()
    {
        $patchFile = $this->getEnvironment()->getBasePath() . '/app/etc/applied.patches.list';
        if (!is_file($patchFile)) {
            throw new NoPatchFileException();
        }
        if (!is_readable($patchFile)) {
            throw new FailedCheckException("app/etc/applied.patches.list not readable");
        }
        $this->_readPatchFile($patchFile);

        return self::SUCCESS;
    }

    /**
     * Get Check code.
     * @return string
     */
    function getCode()
    {
        return "ptch";
    }

    /**
     * Get description.
     * @return string
     */
    function getDescription()
    {
        return "Check installed patches";
    }

    /**
     * Read Patch File.
     * @param $patchFile
     */
    private function _readPatchFile($patchFile)
    {
        $this->log->trace("List installed patches from patch file");
        $handle = fopen($patchFile, "r");
        while (($line = fgets($handle)) !== false) {
            if (!preg_match('/\|/', $line)) {
                continue;
            }
            $line = explode('|', $line);
            $this->success(sprintf("Found %s installed patch (%s)", trim($line[1]), trim($line[0])));
        }
        fclose($handle);
    }

    /**
     * Get position.
     * @return int
     */
    public function getPosition()
    {
        return 70;
    }
}