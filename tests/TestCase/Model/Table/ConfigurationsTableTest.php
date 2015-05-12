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
namespace Configurations\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Configurations\Model\Table\ConfigurationsTable Test Case
 */
class ConfigurationsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'configurations' => 'plugin.settings.settings_configurations'
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
     * test Entity
     *
     * @return void
     */
    public function testEntity()
    {
        $this->assertEquals(0, $this->Settings->find('all')->count());

        $data = [
            'key' => 'App.Key',
            'value' => 'Value',
            'description' => 'Custom Description',
            'type' => 'text',
            'editable' => true,
            'options' => '{"0":"Val0","1":"Val1"}',
            'weight' => 10,
            'autoload' => true
        ];

        $this->Settings->save($this->Settings->newEntity($data));

        $this->assertEquals(1, $this->Settings->find('all')->count());

        $entity = $this->Settings->get(1);

        $this->assertEquals('App.Key', $entity->name);
        $this->assertEquals('App.Key', $entity->key);
        $this->assertEquals('Value', $entity->value);
        $this->assertEquals('Custom Description', $entity->description);
        $this->assertEquals('text', $entity->type);
        $this->assertEquals(1, $entity->editable);
        $this->assertEquals('{"0":"Val0","1":"Val1"}', $entity->options);
        $this->assertEquals([0 => 'Val0', 1 => 'Val1'], $entity->options_array);
        $this->assertEquals(10, $entity->weight);
        $this->assertEquals(1, $entity->autoload);
    }
}
