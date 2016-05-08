<?php
namespace App\Helper;



class Log {

function add($user,$activity){
  $db = new \App\Helper\Connection();
  $sql= "INSERT INTO aktivitas (id,aktifitas,user_id) values ('".uniqid()."','".$activity."','".$user."')";
  $db->execute($sql);

}

function get($group,$page){
  $perpage = 3;
  $index= ($page-1)*$perpage;
  $db = new \App\Helper\Connection();
  $sql= "SELECT id, email, display_name, aktivitas.user_id, waktu, aktifitas
  FROM `aktivitas`join user on user.user_id=aktivitas.`user_id`
  where user.user_group like '".$group."' order by waktu desc limit ".$index.",".$perpage;
  return $db->execute($sql);
}


}
