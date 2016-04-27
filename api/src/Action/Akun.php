<?php
namespace App\Action;


class Akun{

  function __construct()
  {
    $this->akunmodel= new \App\Model\Akun();
  }

  function show($args,$data,$userdata)
  {
    //get user data
    //get user group
    
    if (array_key_exists('cond',$args)){
      $all_cond=explode('&',$args['cond']);
      $newcondition= array();
      foreach ($all_cond as $cond) {
         $condition=explode('=',$cond);
         $newcondition[$condition[0]]=$condition[1];
      }
      $this->akunmodel->find = $newcondition;
    }

    return $this->akunmodel->show();
  }

  function update($args,$data,$userdata){
    //get user data
    //get user group


    //retreive information from request
    $request=$data;

    $this->akunmodel->find=array('id_akun'=>$args['id_akun']);
    $data= array(
      'id_akun'     => $request['id_akun'],
      'nama_akun'   => $request['nama_akun'],
      'jenis_akun'  => $request['jenis_akun']
    );
    $this->akunmodel->update($data);

    return array('status'=>'ok');
  }

  function add($args,$data,$userdata){
    //get user group


    //retreive information from request
    $request=$data;

    $this->akunmodel->id_akun=$request['id_akun'];
    $this->akunmodel->nama_akun=$request['nama_akun'];
    $this->akunmodel->jenis_akun=$request['jenis_akun'];
    $this->akunmodel->add();

    return array('status'=>'ok');
  }

  function delete($args,$data,$userdata){
    //get user group


    $this->akunmodel->find=array('id_akun'=>$args['id_akun']);
    $this->akunmodel->delete();

    return array('status'=>'ok');
  }


}
