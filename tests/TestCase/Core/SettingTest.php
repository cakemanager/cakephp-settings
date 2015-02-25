<?php

namespace Settings\Test\TestCase\Core;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Settings\Core\Setting;

/**
 * Settings\Core\Setting Test Case
 */
class SettingTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Configurations' => 'plugin.settings.configurations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Settings = TableRegistry::get('Configurations');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Settings);

        parent::tearDown();
    }

    /**
     * Test model-method
     *
     * @return void
     */
    public function testModel()
    {

        $model = Setting::model();

        $this->assertEquals('configurations', $model->table());
        $this->assertEquals('Configurations', $model->alias());
    }

    /**
     * Test check-method
     */
    public function testCheck()
    {

        $this->assertFalse(Setting::check('App.Name'));

        Setting::write('App.Name', 'Test');

        $this->assertTrue(Setting::check('App.Name'));
    }

    /**
     * Test write-method
     */
    public function testWrite()
    {
        
    }

}
