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
namespace Settings\Core;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class Setting
{
    /**
     * List of loaded data
     *
     * @var array
     */
    protected static $_data = [];

    /**
     * Holder for the model
     *
     * @var \Cake\ORM\Table
     */
    protected static $_model = null;

    /**
     * Keeps the boolean if the autoload method has been loaded
     *
     * @var bool
     */
    protected static $_autoloaded = false;

    /**
     * read
     *
     * Method to read the data.
     *
     * @param string $key Key with the name of the setting.
     * @param string $type The type to return in.
     * @return mixed
     */
    public static function read($key = null, $type = null)
    {
        self::autoLoad();

        if (!$key) {
            return self::$_data;
        }

        if (key_exists($key, self::$_data)) {
            if ($type) {
                settype(self::$_data[$key], $type);
                return self::$_data[$key];
            }
            return self::$_data[$key];
        }

        $model = self::model();

        $data = $model->findByName($key)->select('value');

        if ($data->count() > 0) {
            $data = $data->first()->toArray();
        } else {
            return null;
        }

        self::_store($key, $data['value']);

        if ($type) {
            settype($data['value'], $type);
        }

        return $data['value'];
    }

    /**
     * write
     *
     * Method to write data to database.
     *
     * ### Example
     *
     * Setting::write('Plugin.Autoload', true);
     *
     * ### Options
     *  - editable  value if the setting is editable in the admin-area. Default 1 (so, editable)
     *  - overrule  boolean if the setting should be written if it already exists. Default true.
     *
     * Example:
     * Setting::write('Plugin.Autoload', false, [
     *      'overrule' => true,
     *      'editable' => 0,
     * ]
     *
     * @param string $key Key of the value. Must contain an prefix.
     * @param mixed $value The value of the key.
     * @param array $options Options array.
     * @return bool
     */
    public static function write($key, $value = null, $options = [])
    {
        self::autoLoad();

        $_options = [
            'editable' => 1,
            'overrule' => true,
        ];

        $options = Hash::merge($_options, $options);

        $model = self::model();

        if (self::check($key)) {
            if ($options['overrule']) {
                $data = $model->findByName($key)->first();
                $data->value = $value;
                $model->save($data);
            } else {
                return false;
            }
        } else {
            $data = $model->newEntity();
            $data->name = $key;
            $data->value = $value;
            $data->editable = $options['editable'];
            $model->save($data);
        }

        self::_store($key, $value);

        return true;
    }

    /**
     * check
     *
     * Checks if an specific key exists.
     * Returns boolean.
     *
     * @param string $key Key.
     * @return bool
     */
    public static function check($key)
    {
        self::autoLoad();
        $model = self::model();

        if (key_exists($key, self::$_data)) {
            return true;
        }

        $query = $model->findByName($key);

        if (!$query->Count()) {
            return false;
        }

        return true;
    }

    /**
     * model
     *
     * Returns an instance of the Configurations-model (Table).
     *
     * @return \Cake\ORM\Table
     */
    public static function model()
    {
        if (!self::$_model) {
            self::$_model = TableRegistry::get('Settings.Configurations');
        }

        return self::$_model;
    }

    /**
     * register
     *
     * Registers a setting and its default values.
     *
     * @param string $key The key.
     * @param mixed $value The default value.
     * @param array $data Custom data
     * @return void
     */
    public static function register($key, $value, $data = [])
    {
        self::autoLoad();

        $_data = [
            'value' => $value,
            'editable' => 1,
            'autoload' => true,
            'description' => null,
        ];

        $data = array_merge($_data, $data);

        // Don't overrule
        $data['overrule'] = false;

        self::write($key, $data['value'], $data);
    }

    /**
     * autoLoad
     *
     * AutoLoad method.
     * Loads all configurations who are autoloaded.
     *
     * @return void
     */
    public static function autoLoad()
    {
        if (self::$_autoloaded) {
            return;
        }
        self::$_autoloaded = true;

        $model = self::model();

        $query = $model->find('all')->where(['autoload' => 1])->select(['name', 'value']);

        foreach ($query as $configure) {
            self::_store($configure->get('name'), $configure->get('value'));
        }
    }

    /**
     * _store
     *
     * Stores recent data in the $_data-variable.
     *
     * @param string $key The key.
     * @param mixed $value The value.
     * @return void
     */
    protected static function _store($key, $value)
    {
        self::$_data[$key] = $value;
    }
}
