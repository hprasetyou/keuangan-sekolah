<?php
namespace App\Action;

class Transaksi{
  function __construct()
  {
    //load model
    $this->transaksimodel= new \App\Model\Transaksi();
    $this->jurnalmodel = new \App\Model\Jurnal();
  }

  function add($args,$data,$userdata)
  {
      $this->transaksimodel->dbname= 'KSAAS_'.$userdata->user_group;
      $this->jurnalmodel->dbname= 'KSAAS_'.$userdata->user_group;

    
      $new_id = uniqid();
      //set properties for new transaksi
      $this->transaksimodel->id_transaksi       = $new_id;
      $this->transaksimodel->id_jenis_transaksi = $args['id_jenis_transaksi'];
      $this->transaksimodel->pencatat           = $userdata->user_id;
      $this->transaksimodel->uraian              = $data['uraian'];
      //add new transaksi
      $this->transaksimodel->add();


      //set properties for jurnal;
      $this->jurnalmodel->id_transaksi  = $this->transaksimodel->id_transaksi;
      $this->jurnalmodel->akun_debet    = $data['debet'];
      $this->jurnalmodel->akun_kredit   = $data['kredit'];
      $this->jurnalmodel->nominal       = $data['jumlah'];

      //jurnal post
      $this->jurnalmodel->post();

      return array('status'=>'ok');

  }

  function show($args,$data,$userdata){
      //show transaksi
  }


}
