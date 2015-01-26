<?php

namespace Settings\Controller\Events;

use Cake\Event\EventListenerInterface;

class ManagerEvents implements EventListenerInterface
{

    public function implementedEvents() {
        return [
            'Component.Manager.beforeFilter.Admin' => 'beforeFilter',
        ];
    }

    public function beforeFilter($event) {
        $component = $event->subject();
        $controller = $component->Controller;

        $controller->Menu->add('Settings', [
            'url' => [
                'prefix'     => 'admin',
                'plugin'     => 'Settings',
                'controller' => 'Settings',
                'action'     => 'prefix'
            ]
        ]);
    }

}
