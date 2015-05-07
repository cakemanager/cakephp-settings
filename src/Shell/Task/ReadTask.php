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
namespace Settings\Shell\Task;

use Cake\Console\Shell;
use Settings\Core\Setting;

class ReadTask extends Shell
{

    /**
     * initialize
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Settings.Configurations');
    }

    /**
     * main
     *
     * @return void
     */
    public function main()
    {
        $key = $this->args[0];

        $this->out("Key: \t \t" . $key);
        $this->out("Value: \t \t" . Setting::read($key));


        if ($this->params['info']) {
            $data = $this->__getByKey($key);

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

    /**
     * getByKey
     *
     * Gets a specific value by key.
     *
     * @param string $key The key.
     * @return string|null
     */
    private function __getByKey($key = null)
    {
        $query = $this->Configurations->find('all')->where(['name' => $key]);

        $count = $query->count();

        if ($count > 0) {
            $data = $query->first();
            return $data;
        }

        return null;
    }

    /**
     * getOptionParser
     *
     * @return ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addArgument('key', [
            'required' => true,
            'help' => __('The key of your setting'),
        ]);

        $parser->addOption('info', [
            'short' => 'i',
            'help' => __('Gets all other data from the setting'),
            'boolean' => true
        ]);

        return $parser;
    }
}
