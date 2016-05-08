<?php
namespace App\Helper;

class Setting{


  public static function get()
     {
         return array(
  //          "root"=>"http://localhost/web/keuangansekolah",
            "root"=>"http://keuangansekolah-hprasetyou.rhcloud.com",
  //           "path"=>"web/keuangansekolah/api/index.php",
             "path"=>"api/index.php",
             "db"=>array(
                "username"=>"hprasetyou",
                "password"=>"Rp.15000", //Rp.15000
                "dbname"=>"KSAAS_main",
//                "dbhost"=>"localhost" //127.3.136.2
               "dbhost"=>"127.3.136.2" //
             )
         );
     }

}
