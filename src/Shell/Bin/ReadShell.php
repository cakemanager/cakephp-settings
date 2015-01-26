<?php

namespace Settings\Shell;

use Cake\Console\Shell;
use Settings\Core\Setting;

/**
 * Write shell command.
 */
class ReadShell extends Shell
{

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main() {

        $key = $this->args[0];

        $this->out("Key: \t".$key);
        $this->out("Value: \t".Setting::read($key));
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();

        $parser->addArgument('key', [
            'required' => true,
            'help'     => __('The key of your setting'),
        ]);

        return $parser;
    }

}
