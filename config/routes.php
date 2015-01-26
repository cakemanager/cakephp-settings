<?php
use Cake\Routing\Router;

Router::prefix('admin', function ($routes) {
    $routes->plugin('Settings', ['path' => '/settings'], function ($routes) {
        $routes->fallbacks('InflectedRoute');
    });
    $routes->fallbacks('InflectedRoute');
});