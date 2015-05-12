<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
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
        'Configurations' => 'plugin.settings.settings_configurations'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Settings = TableRegistry::get('Settings.Configurations');

        Setting::model($this->Settings);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Settings);

        Setting::clear();

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

        $this->assertEquals($this->Settings, $model);

        $this->assertEquals('settings_configurations', $model->table());
        $this->assertEquals('Configurations', $model->alias());

        $_model = "Test";

        Setting::model($_model);

        $this->assertEquals($_model, "Test");
    }

    /**
     * Test clear
     *
     * @return void
     */
    public function testClear()
    {
        Setting::write('App.test1', 1);
        Setting::write('App.test2', 2);
        Setting::write('App.test3', 3);

        $data = [
            'App.test1' => 1,
            'App.test2' => 2,
            'App.test3' => 3,
        ];

        $this->assertEquals($data, Setting::read());

        $this->assertEquals(3, $this->Settings->find('all')->count());

        Setting::clear();

        $this->assertEquals([], Setting::read());

        $this->assertEquals(3, $this->Settings->find('all')->count());
    }

    /**
     * Test check-method
     */
    public function testCheck()
    {
        $this->assertFalse(Setting::check('App.UniqueName'));

        Setting::write('App.UniqueName', 'Test');

        $this->assertTrue(Setting::check('App.UniqueName'));
    }

    public function testRead()
    {
        $data = [
            'name' => 'App.UniqueReadvalue',
            'value' => 'UniqueValue'
        ];

        $this->Settings->save($this->Settings->newEntity($data));

        $this->assertEquals('UniqueValue', Setting::read('App.UniqueReadvalue'));
        $this->assertTrue(Setting::read('App.UniqueReadvalue', 'bool'));
        $this->assertEquals(0, Setting::read('App.UniqueReadvalue', 'integer'));
        $this->assertEquals('UniqueValue', Setting::read('App.UniqueReadvalue', 'string'));

        Setting::write('App.UniqueReadvalue', false);
        $this->assertFalse(Setting::read('App.UniqueReadvalue'));
        $this->assertFalse(Setting::read('App.UniqueReadvalue', 'bool'));
        $this->assertEquals(0, Setting::read('App.UniqueReadvalue'));
        $this->assertEquals(0, Setting::read('App.UniqueReadvalue', 'integer'));
        $this->assertEquals('', Setting::read('App.UniqueReadvalue', 'string'));

        Setting::write('App.UniqueReadvalue', true);
        $this->assertTrue(Setting::read('App.UniqueReadvalue'));
        $this->assertTrue(Setting::read('App.UniqueReadvalue', 'bool'));
        $this->assertEquals(1, Setting::read('App.UniqueReadvalue'));
        $this->assertEquals(1, Setting::read('App.UniqueReadvalue', 'integer'));
        $this->assertEquals('1', Setting::read('App.UniqueReadvalue', 'string'));
    }

    /**
     * Test write-method
     *
     * @return void
     */
    public function testWrite()
    {
        $count = $this->Settings->find('all')->count();

        $this->assertEquals(0, $count);

        Setting::write('App.WriteSimple', 'SimpleValue');

        $this->assertEquals(1, $this->Settings->find('all')->count());
        $value = $this->Settings->get(1);
        $this->assertEquals('App.WriteSimple', $value->name);
        $this->assertEquals('App.WriteSimple', $value->key);
        $this->assertEquals('SimpleValue', $value->value);
        $this->assertEmpty($value->description);
        $this->assertEmpty($value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEmpty($value->options);
        $this->assertEquals(0, $value->weight);

        Setting::write('App.WriteAdvanced', 'AdvancedValue', [
            'description' => 'Short description',
            'type' => 'text',
            'editable' => true,
            'options' => '{"0":"Val0","1":"Val1","2":"Val2"}',
            'weight' => 20,
            'autoload' => true,
        ]);

        $this->assertEquals(2, $this->Settings->find('all')->count());

        $_options = [
            0 => 'Val0',
            1 => 'Val1',
            2 => 'Val2',
        ];
        $value = $this->Settings->get(2);
        $this->assertEquals('App.WriteAdvanced', $value->name);
        $this->assertEquals('App.WriteAdvanced', $value->key);
        $this->assertEquals('AdvancedValue', $value->value);
        $this->assertEquals('Short description', $value->description);
        $this->assertEquals('text', $value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEquals('{"0":"Val0","1":"Val1","2":"Val2"}', $value->options);
        $this->assertEquals($_options, $value->options_array);
        $this->assertEquals(20, $value->weight);
        $this->assertEquals(1, $value->autoload);
    }

    /**
     * Test register-method
     *
     * @return void
     */
    public function testRegister()
    {
        $count = $this->Settings->find('all')->count();

        $this->assertEquals(0, $count);

        Setting::register('App.WriteSimple', 'SimpleValue');

        $this->assertEquals(1, $this->Settings->find('all')->count());
        $value = $this->Settings->get(1);
        $this->assertEquals('App.WriteSimple', $value->name);
        $this->assertEquals('App.WriteSimple', $value->key);
        $this->assertEquals('SimpleValue', $value->value);
        $this->assertEmpty($value->description);
        $this->assertEmpty($value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEmpty($value->options);
        $this->assertEquals(0, $value->weight);

        Setting::write('App.WriteSimple', 'SecondValue');

        $this->assertEquals('SecondValue', Setting::read('App.WriteSimple'));
        $this->assertEquals('SecondValue', $this->Settings->get(1)->value);

        Setting::register('App.WriteSimple', 'SimpleValue');

        $this->assertEquals(1, $this->Settings->find('all')->count());
        $this->assertEquals('SecondValue', Setting::read('App.WriteSimple'));
        $this->assertEquals('SecondValue', $this->Settings->get(1)->value);

        Setting::register('App.WriteAdvanced', 'AdvancedValue', [
            'description' => 'Short description',
            'type' => 'text',
            'editable' => true,
            'options' => '{"0":"Val0","1":"Val1","2":"Val2"}',
            'weight' => 20,
            'autoload' => true,
        ]);

        $this->assertEquals(2, $this->Settings->find('all')->count());

        $_options = [
            0 => 'Val0',
            1 => 'Val1',
            2 => 'Val2',
        ];
        $value = $this->Settings->get(2);
        $this->assertEquals('App.WriteAdvanced', $value->name);
        $this->assertEquals('App.WriteAdvanced', $value->key);
        $this->assertEquals('AdvancedValue', $value->value);
        $this->assertEquals('Short description', $value->description);
        $this->assertEquals('text', $value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEquals('{"0":"Val0","1":"Val1","2":"Val2"}', $value->options);
        $this->assertEquals($_options, $value->options_array);
        $this->assertEquals(20, $value->weight);
        $this->assertEquals(1, $value->autoload);
    }

    /**
     * Test autoload-method
     *
     * @return void
     */
    public function testAutoload()
    {
        Setting::write('App.Test1', 'Test1');
        Setting::write('App.Test2', 'Test2');
        
        Setting::clear();
        
        $this->assertEmpty(Setting::read());
        
        Setting::clear(true);
        Setting::autoLoad();
        
        $_array = [
            'App.Test1' => 'Test1',
            'App.Test2' => 'Test2',
        ];
        
        $this->assertEquals($_array, Setting::read());
    }
}
