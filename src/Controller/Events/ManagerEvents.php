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
namespace Settings\Controller\Events;

use Cake\Event\EventListenerInterface;

class ManagerEvents implements EventListenerInterface
{

    /**
     * implementedEvents
     *
     * Returns a list of all events wich are implemented.
     *
     * @return array
     */
    public function implementedEvents()
    {
        $events = [
            'Component.Manager.beforeFilter.admin' => 'beforeFilter',
        ];

        return $events;
    }

    /**
     * beforeFilter
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeFilter($event)
    {
        $component = $event->subject();
        $controller = $component->Controller;

        $controller->Menu->add('Settings', [
            'url' => [
                'prefix' => 'admin',
                'plugin' => 'Settings',
                'controller' => 'Settings',
                'action' => 'prefix'
            ]
        ]);
    }
}
