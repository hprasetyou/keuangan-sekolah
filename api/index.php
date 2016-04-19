<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); ini_set('display_errors','On');
require 'vendor/autoload.php';

use Phroute\Phroute\RouteCollector;


$path= 'api/index.php';

$router = new RouteCollector();
$router->get($path.'/',function(){
    return 'halooo';
});
$router->get($path.'/haha', function(){
    return App\Action\User::tampil();
});

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Print out the value returned from the dispatched function
echo $response;
