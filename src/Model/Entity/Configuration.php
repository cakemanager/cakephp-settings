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
        'options' => true,
        'weight' => true,
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
     * _getOptions
     *
     * Getter for `options`. Array's are json-decoded.
     *
     * @param string $options Options.
     * @return array
     */
    protected function _getOptions($options)
    {
        if (!empty($options)) {
            return json_decode($options, true);
        }

        return $options;
    }
    
    /**
     * _setOptions
     *
     * Setter for `options`. Array's are json-encoded.
     *
     * @param array $options Options.
     * @return string.
     */
    protected function _setOptions($options)
    {
        return json_encode($options);
    }
}
