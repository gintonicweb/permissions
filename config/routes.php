<?php
use Cake\Routing\Router;

Router::scope('/', ['plugin' => 'Permissions'], function ($routes) {

    $routes->prefix('admin', function ($routes) {
        $routes->connect('/permissions', ['controller' => 'Permissions']);
        $routes->connect('/permissions/:action/*', ['controller' => 'Permissions'], ['routeClass' => 'DashedRoute']);
    });

    $routes->prefix('api', function ($routes) {
        $routes->extensions(['json','xml']);
        $routes->connect('/permissions', ['controller' => 'Permissions']);
        $routes->connect('/permissions/:action/*', ['controller' => 'Permissions'], ['routeClass' => 'DashedRoute']);
        $routes->resources('Permissions.Permissions');
    });
});
