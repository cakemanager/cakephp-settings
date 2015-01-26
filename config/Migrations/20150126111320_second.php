<?php

use Phinx\Migration\AbstractMigration;

class Second extends AbstractMigration
{

    /**
     * Migrate Up.
     *
     * @return void
     */
    public function up() {

        $table = $this->table('configurations');
        $table
                ->addColumn('autoload', 'integer', [
                    'limit'   => '11',
                    'null'    => '',
                    'default' => '1',
                ])
                ->update();
    }

    /**
     * Migrate Down.
     *
     * @return void
     */
    public function down() {

        $table = $this->table('configurations');

        $table
                ->removeColumn('autoload');

    }

}
