<?php
namespace App\Action;


class Sekolah{

  public function __construct(){
    $this->sekolahmodel= new \App\Model\Sekolah();
  }

  public function tampil($args){
          if (array_key_exists('cond',$args)){
              $all_cond=explode('&',$args['cond']);
              $newcondition= array();
              foreach ($all_cond as $cond) {
                 $condition=explode('=',$cond);
                 $newcondition[$condition[0]]=$condition[1];
              }
                $this->sekolahmodel->find = $newcondition;
              }

          return $this->sekolahmodel->show()->data;
  }

  public function update($args,$data){
    $this->sekolahmodel->update($args['id'],$data);
        return array('status'=>'ok');
  }

};
