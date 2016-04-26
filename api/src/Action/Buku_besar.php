<?php
namespace App\Action;


class Buku_besar{
      function __construct()
      {
        $this->jurnalmodel= new \App\Model\Jurnal();
      }



    function show($args,$data,$userdata){
    //show transaksi
    //tujuan: tampilkan daftar jurnal buku besar dalam kurun waktu yang ditentukan
    //id_akun, tanggal_mulai, tanggal_akhir



    //get user group
    $this->jurnalmodel->dbname= 'KSAAS_'.$userdata->user_group;

    $this->jurnalmodel->find= array(
      'tanggal_mulai' => $args['tanggal_mulai'],
      'tanggal_akhir' => $args['tanggal_akhir']
    );
    if($args['akun']<>'all'){
    $this->jurnalmodel->find['akun'] = $args['akun'];
    }
    else{

    }



    if ((array_key_exists('start',$args)) && (array_key_exists('length',$args))){
      $this->jurnalmodel->limit= 'limit '.$args['start'].', '.$args['length'];
    }
    else{

    }
    $output=$this->jurnalmodel->show();
    return array('data'=>$output->data,'num_rows'=>$output->num_rows);

    }

    function saldo($args,$data,$userdata){

      //get user group
      $this->jurnalmodel->dbname= 'KSAAS_'.$userdata->user_group;
      $this->jurnalmodel->find= array(
              'akun'          => $args['akun'],
              'tanggal_mulai' => '2014-01-01',
              'tanggal_akhir' => date('Y-m-d')
            );

      return $this->jurnalmodel->sum()->data;


    }

    function saldo_per_jenis($args,$data,$userdata){

      //get user group
      $this->jurnalmodel->dbname= 'KSAAS_'.$userdata->user_group;

      $this->jurnalmodel->find= array(
        'jenis'          => $args['jenis'],
        'tanggal_mulai' => '2014-01-01',
        'tanggal_akhir' => date('Y-m-d')
      );

      return $this->jurnalmodel->sum_per_jenis()->data;

    }
}
