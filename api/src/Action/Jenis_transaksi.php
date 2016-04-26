<?php
namespace App\Action;

class Jenis_transaksi{
  function __construct(){
    $this->jenis_transaksimodel=  new \App\Model\Jenis_transaksi();
  }

  function add($args,$data,$userdata){
        $this->jenis_transaksimodel->dbname= 'KSAAS_'.$userdata->user_group;

        $id='J-'.substr(md5(uniqid()),0,5);
        $id_jenis=$id;

        $request=$data;
        $this->jenis_transaksimodel->id                =  $id_jenis;
        $this->jenis_transaksimodel->nm_jenis_trans    =  $request['nm_jenis_trans'];
        $this->jenis_transaksimodel->rencana_anggaran  =  $args['id_anggaran'];
        $this->jenis_transaksimodel->sumber_dana       =  '';
        $this->jenis_transaksimodel->parent            =  'root';
        $this->jenis_transaksimodel->nominal           =  '0';
        $this->jenis_transaksimodel->extra             = '';
        $this->jenis_transaksimodel->add();

        if(is_array($request['sub'])){

                  $i=0;
                  foreach ($request['sub'] as $subjenis) {
                     $this->jenis_transaksimodel->id                =  $id_jenis.'-'.$i;
                     $this->jenis_transaksimodel->nm_jenis_trans    =  $subjenis['nm_jenis_trans'];
                     $this->jenis_transaksimodel->rencana_anggaran  =  $args['id_anggaran'];
                     $this->jenis_transaksimodel->sumber_dana       =  '';
                     $this->jenis_transaksimodel->parent            =  $id_jenis;
                     $this->jenis_transaksimodel->nominal           =  $subjenis['nominal'];
                     $this->jenis_transaksimodel->extra             = '{"debet":"'.$subjenis['debet'].'","kredit":"'.$subjenis['kredit'].'"}';
                     $this->jenis_transaksimodel->keterangan        =  $subjenis['keterangan'];
                     $this->jenis_transaksimodel->add();
                     $i++;
                  }

        }

        return array('status'=>'ok','jenis'=>$request['jenis_trans']);

  }

  function delete($args,$data,$userdata){
    //get user group
    $this->jenis_transaksimodel->dbname = 'KSAAS_'.$userdata->user_group;

    $this->jenis_transaksimodel->find=array(
        'rencana_anggaran'  =>  $args['id_anggaran'],
        'id'                =>  $args['id_jenis']
    );
    $this->jenis_transaksimodel->delete();

    return array('status'=>'ok');
  }

  function detail($args,$data,$userdata){
    $this->jenis_transaksimodel->dbname = 'KSAAS_'.$userdata->user_group;

    $this->jenis_transaksimodel->find=array(
        'rencana_anggaran'  =>  $args['id_anggaran'],
        'id'                =>  $args['id_jenis']
    );
    $debet=json_decode($this->jenis_transaksimodel->show()->data[0]->extra);
    $out='';
    foreach ($debet->extra as $key => $value) {
      $out .= $value;
    }
    return $out;
  }




}
