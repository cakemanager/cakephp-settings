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
namespace Settings\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ConfigurationsFixture
 *
 */
class SettingsConfigurationsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    public $fields = [
        'id' => [
            'type' => 'integer'
        ],
        'name' => [
            'type' => 'string',
        ],
        'value' => [
            'type' => 'text',
        ],
        'description' => [
            'type' => 'text',
        ],
        'type' => [
            'type' => 'string',
        ],
        'editable' => [
            'type' => 'integer',
        ],
        'options' => [
            'type' => 'text',
        ],
        'weight' => [
            'type' => 'integer',
        ],
        'autoload' => [
            'type' => 'integer',
            'default' => '1'
        ],
        'created' => [
            'type' => 'datetime',
        ],
        'modified' => [
            'type' => 'datetime',
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB', 'collation' => 'latin1_swedish_ci'
        ],
    ];
}
