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
use Phinx\Migration\AbstractMigration;

class SettingsInitial extends AbstractMigration
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
                'default' => '',
            ])
            ->addColumn('value', 'text', [
                'null' => true
            ])
            ->addColumn('description', 'text', [
                'null' => true
            ])
            ->addColumn('type', 'string', [
                'limit'   => '50',
                'default' => '',
            ])
            ->addColumn('editable', 'integer', [
                'limit'   => '11',
                'default' => '1',
            ])
            ->addColumn('weight', 'integer', [
                'limit'   => '11',
                'default' => '0',
            ])
            ->addColumn('autoload', 'integer', [
                'limit'   => '11',
                'default' => '1',
            ])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
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
