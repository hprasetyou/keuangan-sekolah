<?php
namespace App\Helper;



class Log {

function add($user,$activity){
  $db = new \App\Helper\Connection();
  $sql= "INSERT INTO aktivitas (id,aktifitas,user_id) values ('".uniqid()."','".$activity."','".$user."')";
  $db->execute($sql);

}


}
