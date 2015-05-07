<?php

use Phinx\Migration\AbstractMigration;

class Initial extends AbstractMigration
{

    /**
     * Migrate Up.
     *
     * @return void
     */
    public function up() {
        $table = $this->table('settings_configurations');
        $table
                ->addColumn('name', 'string', [
                    'limit'   => '100',
                    'null'    => '',
                    'default' => '',
                ])
                ->addColumn('value', 'text', [
                    'limit'   => '',
                    'null'    => '',
                    'default' => '',
                ])
                ->addColumn('description', 'text', [
                    'limit'   => '',
                    'null'    => '',
                    'default' => '',
                ])
                ->addColumn('type', 'string', [
                    'limit'   => '50',
                    'null'    => '',
                    'default' => '',
                ])
                ->addColumn('editable', 'integer', [
                    'limit'   => '11',
                    'null'    => '',
                    'default' => '1',
                ])
                ->addColumn('options', 'text', [
                    'limit'   => '',
                    'null'    => '',
                    'default' => '',
                ])
                ->addColumn('weight', 'integer', [
                    'limit'   => '11',
                    'null'    => '',
                    'default' => '0',
                ])
                ->addColumn('created', 'datetime', [
                    'limit'   => '',
                    'null'    => '',
                ])
                ->addColumn('modified', 'datetime', [
                    'limit'   => '',
                    'null'    => '',
                ])
                ->create();
    }

    /**
     * Migrate Down.
     *
     * @return void
     */
    public function down() {

    }

}
