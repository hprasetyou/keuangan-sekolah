<?php
namespace App\Helper;

class Jwt{
  private $key = 'embuh';
  private $algo;
  private $jwt;
  function __construct(){
    $this->jwt = new \Firebase\JWT\JWT();
  }

    public static function encode($input){
        return \Firebase\JWT\JWT::encode($input,'embuh');
    }
    public static function decode($token){
        return \Firebase\JWT\JWT::decode($token,'embuh', array('HS256'));
    }


}
