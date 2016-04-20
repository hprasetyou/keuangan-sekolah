<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); ini_set('display_errors','On');
require 'vendor/autoload.php';

use Phroute\Phroute\RouteCollector;

$setting = \App\Helper\Setting::get();

$data = json_decode(file_get_contents('php://input'), true);

$useraction = new \App\Action\User();

$path= $setting['path'];

$router = new RouteCollector();
$router->get($path.'/',function(){
    return 'halooo';
});
//user route
$router->get($path.'/user', function() use($useraction){
    $args= array ();
    return $useraction->tampil($args);
});

$router->get($path.'/user/{cond}/filter', function($cond) use($useraction){
  $args= array ('cond'=>$cond);
    return $useraction->tampil($args);
});

$router->get($path.'/user/{id}', function($id) use($useraction){
  $args= array ('cond'=>'user_id='.$id);
    return $useraction->tampil($args)->data[0];
});

$router->put($path.'/user/{id}', function($id) use($useraction,$data){
  $args= array ('user_id'=>$id);
    return $useraction->update($args,$data);
});

$router->delete($path.'/user/{id}', function($id) use($useraction,$data){
  $args= array ('user_id'=>$id);
    return $useraction->delete($args,$data);
});

$router->post($path.'/_session', function() use($useraction,$data){
    $args= array ();
    return $useraction->login($args,$data);
});

$router->get($path.'/_session', function() use($useraction,$data){

    $args= array ();
    $embuh=array('name'=>'embuh','iss'=>'haahah');
    try{
        $output= \App\Helper\Auth::reset_timeout();

    }
    catch(Exception $e){
    $output = $e;
    }
    return  $output;
});




//==============================================================
//+++++++++++==============END ROUTE ==============+++++++++++++
//==============================================================
$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

header('Content-Type: application/json');
// Print out the value returned from the dispatched function
echo json_encode($response);
