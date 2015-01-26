<?php
namespace Settings\Test\TestCase\Shell;

use Cake\TestSuite\TestCase;
use Settings\Shell\WriteShell;

/**
 * Settings\Shell\WriteShell Test Case
 */
class WriteShellTest extends TestCase
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
        $this->Write = new WriteShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Write);

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
