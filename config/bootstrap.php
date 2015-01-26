<?php

use Cake\Core\Configure;
use Cake\Event\EventManager;
use Settings\Controller\Events\ManagerEvents;
use Settings\Core\Setting;

// registering the managerEvents
$manager = new ManagerEvents();
EventManager::instance()->attach($manager);

// prefixes
Configure::write('Settings.Prefixes.App', [
    'alias'  => 'Application',
    'prefix' => 'App',
]);

Configure::write('Settings.Prefixes.CM', [
    'alias'  => 'Cake Manager',
    'prefix' => 'CM',
]);

Configure::write('Settings.Prefixes.Bob', [
    'alias'  => 'Bobs info',
    'prefix' => 'Bob',
]);

Configure::write('Settings.Prefixes.Test', [
    'alias'  => 'Test',
    'prefix' => 'Test',
]);
