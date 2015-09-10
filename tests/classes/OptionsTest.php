<?php
/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-09-10 at 11:10:32.
 */
class OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Options
     */
    protected $object;

    /**
     * @covers Options::resetOptions
     */
    public function testResetOptions()
    {
        $this->object->readOptions(array('list', 'help', 'path' => 'test'));
        $this->object->resetOptions();
        $this->assertFalse($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertFalse($this->object->hasPath());
        $this->assertFalse($this->object->getPath());
        $this->assertEmpty($this->object->getChecks());
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Options();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Options::readOptions
     */
    public function testReadOptions()
    {
        $this->object->readOptions(array());
        $this->assertFalse($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertFalse($this->object->hasPath());
        $this->assertFalse($this->object->getPath());
        $this->assertEmpty($this->object->getChecks());
    }

    /**
     * @covers Options::getChecks
     * @depends testReadOptions
     */
    public function testGetChecks()
    {
        $this->object->readOptions(array('checks' => array('a', 'b')));
        $this->assertFalse($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertFalse($this->object->hasPath());
        $this->assertTrue(is_array($this->object->getChecks()));
        $this->assertCount(2, $this->object->getChecks());
        $this->object->readOptions(array('checks' => 'a'));
        $this->assertFalse($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertFalse($this->object->hasPath());
        $this->assertTrue(is_array($this->object->getChecks()));
        $this->assertCount(1, $this->object->getChecks());
    }

    /**
     * @covers Options::isOnlyList
     * @depends testReadOptions
     */
    public function testIsOnlyList()
    {
        $this->object->readOptions(array('list' => true));
        $this->assertTrue($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertFalse($this->object->hasPath());
        $this->assertEmpty($this->object->getChecks());
    }

    /**
     * @covers Options::isHelp
     * @depends testReadOptions
     */
    public function testIsHelp()
    {
        $this->object->readOptions(array('help' => true));
        $this->assertFalse($this->object->isOnlyList());
        $this->assertTrue($this->object->isHelp());
        $this->assertFalse($this->object->hasPath());
        $this->assertEmpty($this->object->getChecks());
    }

    /**
     * @covers Options::hasPath
     * @depends testReadOptions
     */
    public function testHasPath()
    {
        $this->object->readOptions(array('path' => "test"));
        $this->assertFalse($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertTrue($this->object->hasPath());
        $this->assertEmpty($this->object->getChecks());
    }

    /**
     * @covers Options::getPath
     * @depends testReadOptions
     * @depends testHasPath
     */
    public function testGetPath()
    {
        $this->object->readOptions(array('path' => "test"));
        $this->assertFalse($this->object->isOnlyList());
        $this->assertFalse($this->object->isHelp());
        $this->assertEquals("test", $this->object->getPath());
        $this->assertEmpty($this->object->getChecks());
    }
}
