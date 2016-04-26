<?php
namespace App\Action;

class People{
  function __construct(){
    $this->peoplemodel = new \App\Model\People();
  }

  function add($args,$data,$userdata){
    $this->peoplemodel->dbname= 'KSAAS_'.$userdata->user_group;

    //retreive information from request
    $request=$data;

    //set people properties
    $this->peoplemodel->id        = $request['id'];
    $this->peoplemodel->nama      = $request['nama'];
    $this->peoplemodel->info_lain = $request['info_lain'];
    $this->peoplemodel->kelompok  = $request['kelompok'];

    //add new people
    $this->peoplemodel->add();

    return $this->peoplemodel->show();
  }

  function show($args,$data,$userdata){

    $this->peoplemodel->dbname= 'KSAAS_'.$userdata->user_group;

    if (array_key_exists('cond',$args)){
    $all_cond=explode('&',$args['cond']);
    $newcondition= array();
      foreach ($all_cond as $cond) {
           $condition=explode('=',$cond);
           $newcondition[$condition[0]]=$condition[1];
      }
      $this->peoplemodel->find = $newcondition;
    }

    return $this->peoplemodel->show();

  }
}
