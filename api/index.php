<?php

require 'vendor/autoload.php';

use Phroute\Phroute\RouteCollector;

$router = new RouteCollector();
$router->get('/', App\Action\User::tampil());
$router->get('/haha', function(){
    return App\Action\User::tampil();
});

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Print out the value returned from the dispatched function
echo $response;
