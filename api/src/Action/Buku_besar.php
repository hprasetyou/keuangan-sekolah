<?php
namespace App\Action;


class Buku_besar{
      function __construct()
      {
        $this->jurnalmodel= new \App\Model\Jurnal();
        $this->akunmodel = new \App\Model\Akun();
        $this->transaksimodel = new \App\Model\Transaksi();
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
            'akun'=>$dataakun->id_akun,
            'tanggal'=> date('Y-m-d')
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

    function tutup_buku($args,$data,$userdata){
      //1. cari jurnal tutup buku di tahun sebelumnya

      $tapel=$args['tapel'];
      $cektutupbuku = $this->jurnalmodel->tutup_buku_exist($tapel);

      //2. a. jika jurnal tutup buku ada, jangan lakukan apa apa,
        if($cektutupbuku == "1"){
          return array('status'=>'ok');
        }
      //2. b. jika tidak ada, buat jurnal penutupan buku
       else{

          //3. ambil saldo dari tiap tiap akun pendapatan dan beban
          //4. debetkan saldo akun pengeluaran ke rugi laba
          $this->akunmodel->find = array(
            'jenis_akun'=>'b'
          );

          $output=array();
          $i=0;
          foreach ($this->akunmodel->show()->data as $akunbeban) {
            # code...
            $this->jurnalmodel->find=array(
              'akun'=>$akunbeban->id_akun,
              'tanggal'=> '20'.substr($tapel,2,2).'-06-30'
            );
            //saldo akun beban
            $output[$i] = $this->jurnalmodel->saldo()->data[0];
            $this->jurnalmodel->find=array(
              'akun'=>$akunbeban->id_akun,
              'tanggal'=> '20'.substr($tapel,2,2).'-06-30'
            );
            if($this->jurnalmodel->saldo()->data[0]->debet > 0){
                $new_id = uniqid();
                //set properties for new transaksi
                $this->transaksimodel->id_transaksi       = $new_id;
                $this->transaksimodel->id_jenis_transaksi = 'undefined';
                $this->transaksimodel->pencatat           = $userdata->user_id;
                $this->transaksimodel->waktu              = '20'.substr($tapel,2,2).'-06-30 23:57:57';
                $this->transaksimodel->uraian              = 'tutup buku';
                //add new transaksi
                $this->transaksimodel->add();

                $this->jurnalmodel->id_transaksi = $new_id;
                $this->jurnalmodel->akun_debet = '601';
                $this->jurnalmodel->akun_kredit = $akunbeban->id_akun;
                $this->jurnalmodel->nominal =  $this->jurnalmodel->saldo()->data[0]->debet;

                $this->jurnalmodel->post();
            }
           $i++;

          }
          //5. kreditkan saldo akun pendapatan ke rugi laba
          $this->akunmodel->find = array(
            'jenis_akun'=>'p'
          );

          foreach ($this->akunmodel->show()->data as $akunpendapatan) {
            # code...
            $this->jurnalmodel->find=array(
              'akun'=>$akunpendapatan->id_akun,
              'tanggal'=> '20'.substr($tapel,2,2).'-06-31'
            );
            //saldo akun beban
            $output[$i] = $this->jurnalmodel->saldo()->data[0];
            $this->jurnalmodel->find=array(
              'akun'=>$akunpendapatan->id_akun,
              'tanggal'=> '20'.substr($tapel,2,2).'-06-31'
            );
            if($this->jurnalmodel->saldo()->data[0]->kredit > 0){
                $new_id = uniqid();
                //set properties for new transaksi
                $this->transaksimodel->id_transaksi       = $new_id;
                $this->transaksimodel->id_jenis_transaksi = 'undefined';
                $this->transaksimodel->waktu              = '20'.substr($tapel,2,2).'-06-30 23:57:57';
                $this->transaksimodel->pencatat           = $userdata->user_id;
                $this->transaksimodel->uraian             = 'tutup buku';
                //add new transaksi
                $this->transaksimodel->add();

                $this->jurnalmodel->id_transaksi = $new_id;
                $this->jurnalmodel->akun_debet = $akunpendapatan->id_akun ;
                $this->jurnalmodel->akun_kredit = '601';
                $this->jurnalmodel->nominal =  $this->jurnalmodel->saldo()->data[0]->kredit;

                $this->jurnalmodel->post();
            }
           $i++;

          }



          //6. cek saldo rugi laba, jika debet, kreditkan ke akun modal, jika kredit, debetkan ke akun modal
          $this->jurnalmodel->find=array(
            'akun'=>'601',
            'tanggal'=> '20'.substr($tapel,2,2).'-06-31'
          );
          //saldo akun rugi laba
          $saldorl = $this->jurnalmodel->saldo()->data[0];
          //jika posisi rugi laba di debet, maka kreditkan saldo ke akun modal
          if($saldorl->debet > $saldorl->kredit){
              $new_id = uniqid();
              //set properties for new transaksi
              $this->transaksimodel->id_transaksi       = $new_id;
              $this->transaksimodel->id_jenis_transaksi = 'undefined';
              $this->transaksimodel->waktu              = '20'.substr($tapel,2,2).'-06-30 23:57:57';
              $this->transaksimodel->pencatat           = $userdata->user_id;
              $this->transaksimodel->uraian             = 'tutup buku';
              //add new transaksi
              $this->transaksimodel->add();

              $this->jurnalmodel->id_transaksi = $new_id;
              //akun modal
              $this->jurnalmodel->akun_debet = '600' ;
              //akun rl
              $this->jurnalmodel->akun_kredit = '601';
              $this->jurnalmodel->nominal =  $saldorl->debet;

              $this->jurnalmodel->post();
          }else{ //posisi rl ada di kredit, maka debetkan ke modal
              $new_id = uniqid();
              //set properties for new transaksi
              $this->transaksimodel->id_transaksi       = $new_id;
              $this->transaksimodel->id_jenis_transaksi = 'undefined';
              $this->transaksimodel->waktu              = '20'.substr($tapel,2,2).'-06-30 23:57:57';
              $this->transaksimodel->pencatat           = $userdata->user_id;
              $this->transaksimodel->uraian             = 'tutup buku';
              //add new transaksi
              $this->transaksimodel->add();

              $this->jurnalmodel->id_transaksi = $new_id;
              //akun kredit
              $this->jurnalmodel->akun_debet = '601' ;
              //akun modal
              $this->jurnalmodel->akun_kredit = '600';
              $this->jurnalmodel->nominal =  $saldorl->kredit;

              $this->jurnalmodel->post();
          }

          return array('status'=>'ok', 'msg'=>'tutup buku berhasil');
        }


    }
}
