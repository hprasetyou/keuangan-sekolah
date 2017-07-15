<?php
namespace App\Helper;

class Setting{


  public static function get()
     {
         return array(
            "root"=>"http://localhost/web/keuangansekolah",
             "path"=>"keuangansekolah/api/index.php",
             "db"=>array(
                "username"=>"root",
                "password"=>"", 
                "dbname"=>"KSAAS_main",
                "dbhost"=>"localhost"
             ),
             //email config
             "mail"=>array(
               "host"=>"ssl://smtp.gmail.com",
               "username"=>"",
               "password"=>"",
               "port"=>465
               )
         );
     }

}
