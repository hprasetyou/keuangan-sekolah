<?php
namespace App\Action;


class Buku_besar{
      function __construct()
      {
        $this->jurnalmodel= new \App\Model\Jurnal();
        $this->akunmodel = new \App\Model\Akun();
      }



    function show($args,$data,$userdata){
    //show transaksi
    //tujuan: tampilkan daftar jurnal buku besar dalam kurun waktu yang ditentukan
    //id_akun, tanggal_mulai, tanggal_akhir



    //get user group


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

    function saldo_per_akun($args,$data,$userdata){

      $i=0;
      $output=[];
        foreach ($this->akunmodel->show()->data as $dataakun) {
          $this->jurnalmodel->find=array(
            'akun'=>$dataakun->id_akun
          );
          $output[$i] = $this->jurnalmodel->saldo()->data[0];
            $i++;
        }
        return $output;
    }


    function saldo_per_jenis($args,$data,$userdata){

      //get user group


      $this->jurnalmodel->find= array(
        'jenis'          => $args['jenis'],
        'tanggal_mulai' => '2014-01-01',
        'tanggal_akhir' => date('Y-m-d')
      );

      return $this->jurnalmodel->sum_per_jenis()->data;

    }

    function get_neraca_lajur($args,$data,$userdata){
      //get user group
      $output= array();
      $tapel=$args['tapel'];
      $i=0;
        foreach ($this->akunmodel->show()->data as $dataakun) {
          # code...
          $output[$i]['id_akun']=$dataakun->id_akun;
          $output[$i]['nama_akun']=$dataakun->nama_akun;
          $output[$i]['saldo']=$this->jurnalmodel->sum($dataakun->id_akun,'saldo',$tapel)->data[0];
          $output[$i]['penyesuaian']=$this->jurnalmodel->sum($dataakun->id_akun,'penyesuaian',$tapel)->data[0];
          $output[$i]['rl']=$this->jurnalmodel->sum($dataakun->id_akun,'rl',$tapel)->data[0];
          $output[$i]['neraca']=$this->jurnalmodel->sum($dataakun->id_akun,'neraca',$tapel)->data[0];
          $i++;
        }
        return $output;
    }
}
