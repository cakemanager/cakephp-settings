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
namespace Settings\Model\Entity;

use Cake\ORM\Entity;
use Settings\Core\Setting;

/**
 * Configuration Entity.
 */
class Configuration extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'key' => true,
        'name' => true,
        'value' => true,
        'description' => true,
        'type' => true,
        'editable' => true,
        'weight' => true,
        'autoload' => true,
    ];

    /**
     * _setKey
     *
     * Setter for the key.
     *
     * @param string $key The value.
     * @return void
     */
    protected function _setKey($key)
    {
        $this->set('name', $key);
    }

    /**
     * _getKey
     *
     * Getter for the key.
     *
     * @return string
     */
    protected function _getKey()
    {
        return $this->get('name');
    }

    /**
     * _getOptionsArray
     *
     * Getter for `options`. Array's are json-decoded.
     *
     * @return array
     */
    protected function _getOptions()
    {
        if (array_key_exists('name', $this->_properties)) {
            $options = Setting::options($this->_properties['name']);
            if (is_callable($options)) {
                return $options();
            }
            return $options;
        }
        return false;
    }

    protected $_virtual = ['options'];
}
