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

  $token =$_SERVER['HTTP_TOKEN'];
      if(isset($token)){
          try{
            $userdata = \App\Helper\Jwt::decode($token);
            if(time() > $userdata->expired){
              $output = new \StdClass;
            $output->error='token expired';
            $output->auth=0;
            }
            else{
              $userdata->auth=1;
              $output = $userdata;
            }
          }
          catch(Exception $e){
            $output = new \StdClass;
            $output->error='token tidak cocok';
            $output->auth=0;
          }
      }
      else {

          $output = new \StdClass;
          $output->error='token belum diset';
          $output->auth=0;
      }
      return $output;
  }

  function reset_timeout(){
    $token = $_SERVER['HTTP_TOKEN'];
    $userdata= \App\Helper\Jwt::decode($token);
    foreach ($userdata as $key => $value) {
      # code...
      $newuserdata[$key] = $value;
    }
    $newuserdata['expired'] = time()+1800;
    return \App\Helper\Jwt::encode($newuserdata);
  }




}
