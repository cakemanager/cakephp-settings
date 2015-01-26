<?php
namespace Settings\Test\TestCase\Shell;

use Cake\TestSuite\TestCase;
use Settings\Shell\SettingShell;

/**
 * Settings\Shell\SettingShell Test Case
 */
class SettingShellTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMock('Cake\Console\ConsoleIo');
        $this->Setting = new SettingShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Setting);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
