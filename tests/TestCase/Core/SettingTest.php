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
        
        $data = [
            'name' => 'App.UniqueArray',
            'value' => 'a:4:{i:0;i:1;i:2;i:3;i:3;s:3:"one";s:3:"two";s:5:"three";}'
        ];

        $this->Settings->save($this->Settings->newEntity($data));
        $read = Setting::read('App.UniqueArray');
        $this->assertGreaterThan(0, count($read));
        $this->assertEquals([1, 2 => 3, 'one', 'two' => 'three'], Setting::read('App.UniqueArray'));
        
        $read = Setting::read('App');
        $this->assertGreaterThan(0, count($read));
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
            'options' => [
                1 => 'One',
                2 => 'Two'
            ],
            'weight' => 20,
            'autoload' => true,
        ]);

        $this->assertEquals(2, $this->Settings->find('all')->count());

        $value = $this->Settings->get(2);
        $this->assertEquals('App.WriteAdvanced', $value->name);
        $this->assertEquals('App.WriteAdvanced', $value->key);
        $this->assertEquals('AdvancedValue', $value->value);
        $this->assertEquals('Short description', $value->description);
        $this->assertEquals('text', $value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEquals(20, $value->weight);
        $this->assertEquals(1, $value->autoload);
        
        Setting::write('Plugin.WriteArray', [1, 2 => 3, 'one', 'two' => 'three'], [
            'description' => 'Short description',
            'type' => 'array',
            'editable' => true,
            'options' => [
                1 => 'One',
                2 => 'Two'
            ],
            'weight' => 20,
            'autoload' => true,
        ]);
        
        Setting::write('Plugin.WriteArray.ToAnother', [1, 2 => 3, 'one', 'two' => 'three'], [
            'description' => 'Short description',
            'type' => 'array',
            'editable' => true,
            'options' => [
                1 => 'One',
                2 => 'Two'
            ],
            'weight' => 20,
            'autoload' => true,
        ]);

        $this->assertEquals(4, $this->Settings->find('all')->count());

        $value = $this->Settings->get(3);
        $this->assertEquals('Plugin.WriteArray', $value->name);
        $this->assertEquals('Plugin.WriteArray', $value->key);
        $this->assertEquals('a:4:{i:0;i:1;i:2;i:3;i:3;s:3:"one";s:3:"two";s:5:"three";}', $value->value);
        $this->assertEquals('Short description', $value->description);
        $this->assertEquals('array', $value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEquals(20, $value->weight);
        $this->assertEquals(1, $value->autoload);

        $value = $this->Settings->get(4);
        $this->assertEquals('Plugin.WriteArray.ToAnother', $value->name);
        $this->assertEquals('Plugin.WriteArray.ToAnother', $value->key);
        $this->assertEquals('a:4:{i:0;i:1;i:2;i:3;i:3;s:3:"one";s:3:"two";s:5:"three";}', $value->value);
        $this->assertEquals('Short description', $value->description);
        $this->assertEquals('array', $value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEquals(20, $value->weight);
        $this->assertEquals(1, $value->autoload);

        $value = $this->Settings->find()->select(['name', 'value'])->where(['name LIKE' => 'Plugin.%']);
        $this->assertGreaterThan(0, $value->count());
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
            'weight' => 20,
            'autoload' => true,
        ]);

        $this->assertEquals(2, $this->Settings->find('all')->count());

        $value = $this->Settings->get(2);
        $this->assertEquals('App.WriteAdvanced', $value->name);
        $this->assertEquals('App.WriteAdvanced', $value->key);
        $this->assertEquals('AdvancedValue', $value->value);
        $this->assertEquals('Short description', $value->description);
        $this->assertEquals('text', $value->type);
        $this->assertEquals(1, $value->editable);
        $this->assertEquals(20, $value->weight);
        $this->assertEquals(1, $value->autoload);
        
        Setting::register('App.WriteArray', [1, 2 => 3, 'one', 'two' => 'three'], [
            'description' => 'Short description',
            'type' => 'text',
            'editable' => true,
            'weight' => 20,
            'autoload' => true,
        ]);
    }

    /**
     * Test options-method
     */
    public function testOptions()
    {
        Setting::register('App.Key', 1, [
            'options' => [
                0 => 'One',
                1 => 'Two',
            ]
        ]);

        $expected = [
            0 => 'One',
            1 => 'Two',
        ];

        $this->assertEquals($expected, Setting::options('App.Key'));

        Setting::options('App.Second', [
            0 => 'One',
            1 => 'Two',
        ]);
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
