<?php
namespace App\Helper;

class Auth{
  function __construct(){
  }


  function set_token($input_data){
    $input_data['expired']  =time()+1800;
    return \App\Helper\Jwt::encode($input_data);
  }

  function user_data(){
      $headers = getallheaders();
      try{
        $output = \App\Helper\Jwt::decode($headers['token']);
      }
      catch(Exception $e){
        $output = $e;
      }
      return $output;
  }

  function reset_timeout(){
    $headers = getallheaders();
    $userdata= \App\Helper\Jwt::decode($headers['token']);
    $userdata['expired'] = time()+1800;
    return \App\Helper\Jwt::encode($input_data);
  }




}
