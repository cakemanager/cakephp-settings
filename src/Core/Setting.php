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

use Cake\Datasource\ConnectionManager;
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
     * Options
     *
     * @var array
     */
    protected static $_options = [];

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
        if(!self::_tableExists()) {
            return;
        }

        self::autoLoad();

        if (!$key) {
            return self::$_data;
        }

        if (key_exists($key, self::$_data)) {
            if ($type) {
                $value = self::$_data[$key];
                settype($value, $type);
                return $value;
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

        $value = $data['value'];

        if ($type) {
            settype($value, $type);
        }

        return $value;
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
        if(!self::_tableExists()) {
            return;
        }

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
                if ($data) {
                    $data->set('value', $value);
                    $model->save($data);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            $data = $model->newEntity($options);
            $data->name = $key;
            $data->value = $value;
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
        if(!self::_tableExists()) {
            return;
        }

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
     * Also used as setter for the instance of the model.
     *
     * @param \Cake\ORM\Table|null $model Model to use.
     * @return \Cake\ORM\Table
     */
    public static function model($model = null)
    {
        if ($model) {
            self::$_model = $model;
        }

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
     * @param array $data Custom data.
     * @return void
     */
    public static function register($key, $value, $data = [])
    {
        if(!self::_tableExists()) {
            return;
        }

        self::autoLoad();

        $_data = [
            'value' => $value,
            'editable' => 1,
            'autoload' => true,
            'options' => [],
            'description' => null,
        ];

        $data = array_merge($_data, $data);

        // Don't overrule because we register
        $data['overrule'] = false;

        self::options($key, $data['options']);

        self::write($key, $data['value'], $data);
    }

    /**
     * options
     *
     * @param string $key Key for options.
     * @param array $value Options to use.
     * @return mixed
     */
    public static function options($key, $value = null) {
        if(!self::_tableExists()) {
            return;
        }

        if($value) {
            self::$_options[$key] = $value;
        }

        if(array_key_exists($key, self::$_options)) {
            return self::$_options[$key];
        } else {
            return false;
        }
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
        if(!self::_tableExists()) {
            return;
        }
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
     * clear
     *
     * Clears all settings out of the class. Settings
     * won't be deleted from database.
     *
     * @param bool $reload Bool if settings should be reloaded
     * @return void
     */
    public static function clear($reload = false)
    {
        self::$_autoloaded = !$reload;
        self::$_data = [];
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

    /**
     * _tableExists
     *
     * @return bool
     */
    protected static function _tableExists() {
        $db = ConnectionManager::get('default');
        $tables = $db->schemaCollection()->listTables();

        if(in_array('settings_configurations', $tables)) {
            return true;
        }
        return false;
    }
}
