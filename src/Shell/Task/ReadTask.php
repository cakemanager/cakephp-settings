<?php

namespace Settings\Shell\Task;

use Cake\Console\Shell;
use Settings\Core\Setting;

class ReadTask extends Shell
{

    public function initialize() {
        parent::initialize();

        $this->loadModel('Settings.Configurations');
    }

    public function main() {

        $key = $this->args[0];

        $this->out("Key: \t \t" . $key);
        $this->out("Value: \t \t" . Setting::read($key));


        if ($this->params['info']) {

            $data = $this->getByKey($key);

            if ($data) {

                $this->out("");
                $this->out("ID: \t\t" . $data->id);
                $this->out("Editable: \t" . $data->editable);
                $this->out("Type: \t" . $data->type);
                $this->out("Created: \t" . $data->created);
                $this->out("Modified: \t" . $data->modified);
            }
        }
    }

    private function getByKey($key = null) {

        $query = $this->Configurations->find('all')->where(['name' => $key]);

        $count = $query->count();

        if ($count > 0) {
            $data = $query->first();
            return $data;
        }

        return null;
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        $parser->addArgument('key', [
            'required' => true,
            'help'     => __('The key of your setting'),
        ]);

        $parser->addOption('info', [
            'short'   => 'i',
            'help'    => __('Gets all other data from the setting'),
            'boolean' => true
        ]);

        return $parser;
    }

}
