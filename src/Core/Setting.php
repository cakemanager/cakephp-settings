<?php namespace Settings\Core;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class Setting
{
    /**
     * List of loaded data
     *
     * @var type
     */
    protected static $_data = [];

    /**
     * Holder for the model
     *
     * @var type
     */
    protected static $_model = null;

    /**
     * Keeps the boolean if the autoload method has been loaded
     *
     * @var type
     */
    protected static $_autoloaded = false;

    /**
     * Method to read the data
     *
     * @param string $key with the name of the setting
     * @return type
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
     * Method to write data to database
     *
     * @param string $key of the value. Must contain an prefix
     * @param mixed $value the value of the key
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
     * Checks if an specific key exists
     *
     * Returns true or false
     *
     * @param string $key
     * @return bool true or false
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
     * Returns an instance of the Configurations-model (Table)
     *
     * @return type
     */
    public static function model()
    {
        if (!self::$_model) {
            self::$_model = TableRegistry::get('Settings.Configurations');
        }

        return self::$_model;
    }

    /**
     * Registers a setting and its default values
     *
     * @param type $key
     * @param type $data
     */
    public static function register($key, $data = [])
    {
        self::autoLoad();

        $_data = [
            'value' => null,
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
     * AutoLoad method.
     *
     * Loads all configurations who are autoloaded
     *
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
     * Stores recent data in the $_data-variable
     *
     * @param type $key
     * @param type $value
     */
    protected static function _store($key, $value)
    {
        self::$_data[$key] = $value;
    }
}
