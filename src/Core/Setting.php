<?php
/**
 *
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
     *
     * List of loaded data
     *
     * @var array
     */
    protected static $_data = [];
    
    /**
     *
     * Array of values currently stored in Configure.
     *
     * @var array
     */
    protected static $_values = [];

    /**
     *
     * Options
     *
     * @var array
     */
    protected static $_options = [];

    /**
     *
     * Holder for the model
     *
     * @var \Cake\ORM\Table
     */
    protected static $_model = null;

    /**
     *
     * Keeps the boolean if the autoload method has been loaded
     *
     * @var bool
     */
    protected static $_autoloaded = false;

    /**
     *
     * read
     *
     * Method to read the data.
     *
     * @param string $key  Key with the name of the setting.
     * @param string $type The type to return in.
     * @return mixed
     */
    public static function read($key = null, $type = null)
    {
        if (!self::_tableExists()) {
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
            $data = $model->find()->select(['name', 'value'])->where(['name LIKE' => $key . '.%']);
            
            if ($data->count() > 0) {
                static::$_values = [];
                $data = $data->toArray();
                foreach ($data as $dataSet) {
                    if (self::_serialized($dataSet->value)) {
                        $dataSet->value = unserialize($dataSet->value);
                    }
                    static::$_values = Hash::insert(static::$_values, $dataSet->name, $dataSet->value);
                }
                
                $data['value'] = static::$_values;
            } else {
                return null;
            }
        }

        if (self::_serialized($data['value'])) {
            $data['value'] = unserialize($data['value']);
        }
        self::_store($key, $data['value']);

        $value = $data['value'];

        if ($type) {
            settype($value, $type);
        }

        return $value;
    }

    /**
     *
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
     * @param string $key     Key of the value. Must contain an prefix.
     * @param mixed  $value   The value of the key.
     * @param array  $options Options array.
     * @return void|bool
     */
    public static function write($key, $value = null, $options = [])
    {
        if (!self::_tableExists()) {
            return;
        }

        self::autoLoad();

        $_options = [
            'editable' => 1,
            'overrule' => true,
        ];

        $options = Hash::merge($_options, $options);

        $model = self::model();
        
        if (is_array($value) && !empty($value)) {
            $value = serialize($value);
        }

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
     *
     * check
     *
     * Checks if an specific key exists.
     * Returns boolean.
     *
     * @param string $key Key.
     * @return bool|void
     */
    public static function check($key)
    {
        if (!self::_tableExists()) {
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
     *
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
     *
     * register
     *
     * Registers a setting and its default values.
     *
     * @param string $key   The key.
     * @param mixed  $value The default value.
     * @param array  $data  Custom data.
     * @return void
     */
    public static function register($key, $value, $data = [])
    {
        if (!self::_tableExists()) {
            return;
        }

        self::autoLoad();
        
        if (is_array($value)) {
            $value = serialize($value);
        }

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
     *
     * options
     *
     * @param string $key   Key for options.
     * @param array  $value Options to use.
     * @return mixed
     */
    public static function options($key, $value = null)
    {
        if (!self::_tableExists()) {
            return;
        }

        if ($value) {
            self::$_options[$key] = $value;
        }

        if (array_key_exists($key, self::$_options)) {
            return self::$_options[$key];
        } else {
            return false;
        }
    }

    /**
     *
     * autoLoad
     *
     * AutoLoad method.
     * Loads all configurations who are autoloaded.
     *
     * @return void
     */
    public static function autoLoad()
    {
        if (!self::_tableExists()) {
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
     *
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
     *
     * _store
     *
     * Stores recent data in the $_data-variable.
     *
     * @param string $key   The key.
     * @param mixed  $value The value.
     * @return void
     */
    protected static function _store($key, $value)
    {
        self::$_data[$key] = $value;
    }

    /**
     *
     * _tableExists
     *
     * @return bool
     */
    protected static function _tableExists()
    {
        $db = ConnectionManager::get('default');
        $tables = $db->schemaCollection()->listTables();

        if (in_array('settings_configurations', $tables)) {
            return true;
        }
        return false;
    }
    
    /**
     * _serialized
     *
     * @codeCoverageIgnore
     * @param string $value - The value.
     * @param mixed  $result - The result (null default).
     * @return bool
     */
    protected static function _serialized($value, $result = null)
    {
        if (! is_string($value)) {
            return false;
        }

        if ('b:0;' === $value) {
            $result = false;
            return true;
        }
        $length = strlen($value);
        $end = '';
        
        if (isset($value[0])) {
            switch ($value[0]) {
                case 's':
                    if ('"' !== $value[$length - 2]) {
                        return false;
                    }
                    // no break
                case 'b':
                    // no break
                case 'i':
                    // no break
                case 'd':
                    $end .= ';';
                    // no break
                case 'a':
                    // no break
                case 'O':
                    $end .= '}';
                    
                    if (':' !== $value[1]) {
                        return false;
                    }

                    switch ($value[2]) {
                        case 0:
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                        case 9:
                            break;
        
                        default:
                            return false;
                    }
                    // break appled in embedded switch
                case 'N':
                    $end .= ';';
                
                    if ($value[$length - 1] !== $end[0]) {
                        return false;
                    }
                    break;
                
                default:
                    return false;
            }
        }
        
        if (( $result = unserialize($value) ) === false) {
            $result = null;
            return false;
        }
        
        return true;
    }
}
