<?php

namespace Settings\Shell\Task;

use Cake\Console\Shell;
use Settings\Core\Setting;

class WriteTask extends Shell
{

    public function main() {
        $key = $this->args[0];
        $value = $this->args[1];

        $_params = [
            'editable' => false,
            'type'     => '',
        ];

        $params = array_merge($_params, $this->params);

        Setting::write($key, $value, $params);

        $this->out('The setting has been saved');
        $this->out("Key: \t" . $key);
        $this->out("Value: \t" . Setting::read($key));
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        $parser->addArgument('key', [
            'required' => true,
            'help'     => __('The key of your setting'),
        ]);

        $parser->addArgument('value', [
            'required' => true,
            'help'     => __('The value of your setting'),
        ]);

        $parser->addOption('editable', [
            'short'   => 'e',
            'help'    => __('Ability to edit the setting as admin in your admin-panel'),
            'boolean' => true
        ]);

        $parser->addOption('type', [
            'short'   => 't',
            'help'    => __('What type is the setting?'),
            'choices' => [
                'button',
                'checkbox',
                'color',
                'date',
                'datetime',
                'datetime-local',
                'email',
                'file',
                'hidden',
                'image',
                'month',
                'number',
                'password',
                'radio',
                'range',
                'reset',
                'search',
                'submit',
                'tel',
                'text',
                'textarea',
                'time',
                'url',
                'week',
            ]
        ]);


        return $parser;
    }

}
