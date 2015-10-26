<?php
use Cake\Routing\Router;

Router::scope('/', ['plugin' => 'Permissions'], function ($routes) {

    $routes->fallbacks('DashedRoute');

    $routes->prefix('admin', function ($routes) {
        $routes->connect('/permissions', ['controller' => 'Permissions']);
        $routes->connect('/permissions/:action/*', ['controller' => 'Permissions'], ['routeClass' => 'DashedRoute']);
        $routes->fallbacks('DashedRoute');
    });
});
