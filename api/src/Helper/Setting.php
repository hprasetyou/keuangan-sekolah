<?php
namespace App\Helper;

class Setting{


  public static function get()
     {
         return array(
             "path"=>"api/index.php",
             "db"=>array(
                "username"=>"hprasetyou",
                "password"=>"Rp.15000",
                "dbname"=>"KSAAS_main",
                "dbhost"=>"127.3.136.2"
             )
         );
     }

}
