<?php namespace Settings\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setting Entity.
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

    protected function _setKey($key)
    {
        $this->set('name', $key);
    }

    protected function _getKey()
    {
        return $this->get('name');
    }

    protected function _getOptions($options)
    {
        if (!empty($options)) {
            return json_decode($options, true);
        }

        return $options;
    }
}
