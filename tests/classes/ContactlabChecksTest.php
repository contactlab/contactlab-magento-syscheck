<?php

class ContactlabChecksTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ContactlabChecks
     */
    private $object;

    /**
     * @covers ContactlabChecks::getHelpFile
     */
    public function testGetHelpFile()
    {
        $file = $this->object->getHelpFile();
        $this->assertFileExists($file);
    }

    /**
     * @covers ContactlabChecks::getOptions
     */
    public function testGetOptions()
    {
        $this->assertInstanceOf('Options', $this->object->getOptions());
    }

    /**
     * @covers ContactlabChecks::getConfigFile
     */
    public function testGetConfigFile()
    {
        $this->assertFileExists($this->object->getConfigFile());
    }

    /**
     * @covers ContactlabChecks::readConfiguration
     */
    public function testReadConfiguration()
    {
        $this->object->getOptions()->resetOptions();
        $this->object->getOptions()->readOptions(array('list' => true));
        $this->object->readConfiguration();
        $this->assertInstanceOf('MagentoEnvironment', $this->object->getEnvironment());
        $this->assertInstanceOf('Options', $this->object->getEnvironment()->getOptions());
        $this->assertInstanceOf('stdClass', $this->object->getConfiguration());
        $this->assertNotEmpty($this->object->getConfiguration()->checks);

    }

    /**
     * @covers ContactlabChecks::getConfiguration
     */
    public function testGetConfiguration()
    {
        $this->object->getOptions()->resetOptions();
        $this->object->getOptions()->readOptions(array('list' => true));
        $this->object->readConfiguration();
        $this->assertNotEmpty($this->object->getConfiguration()->checks);
    }

    /**
     * @covers ContactlabChecks::isSocket
     */
    public function testIsSocket()
    {
        $this->assertFalse(ContactlabChecks::isSocket('localhost'));
        $this->assertTrue(ContactlabChecks::isSocket('/opt/mysql/tmp/mysql.sock'));
    }

    protected function setUp()
    {
        $options = new Options();
        $this->object = new ContactlabChecks($options);
    }

    protected function tearDown()
    {
    }
    
    
}
