<?php

namespace Settings\Shell;

use Cake\Console\Shell;

/**
 * Setting shell command.
 */
class SettingShell extends Shell
{

    public $tasks = ['Settings.Read', 'Settings.Write'];

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() {


    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('read', [
            'help' => 'Executes the reader.',
            'parser' => $this->Read->getOptionParser()
        ]);

        $parser->addSubcommand('write', [
            'help' => 'Executes the writer.',
            'parser' => $this->Write->getOptionParser()
        ]);

        return $parser;
    }

}
