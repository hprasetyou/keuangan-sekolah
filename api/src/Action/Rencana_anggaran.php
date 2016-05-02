<?php
namespace App\Action;

class Rencana_anggaran{

  function __construct(){
        $this->rencana_anggaranmodel= new \App\Model\Rencana_anggaran();
        $this->jenis_transaksimodel= new \App\Model\Jenis_transaksi();
  }

  public function tampil($args,$data,$userdata){
          return $this->rencana_anggaranmodel->show()->data;
  }


  function detail($args,$data,$userdata){

          $this->jenis_transaksimodel->dbname = 'KSAAS_'.$userdata->user_group;

          //get rencana anggaran where id defined = params
          $this->rencana_anggaranmodel->find = array(
              'id' => $args['id']
          );
          $data_anggaran = $this->rencana_anggaranmodel->show()->data[0];

          $jenis_trans= array('m'=>'jenis_trans_masuk','k'=>'jenis_trans_keluar');
          foreach ($jenis_trans as $key => $value) {


          //filter kolom
          $this->jenis_transaksimodel->filter='id,nm_jenis_trans';
          //get member of selected rencana anggaran
          //jenis transaksi kategori root
          $this->jenis_transaksimodel->find=array(
            'rencana_anggaran'  => $args['id'],
            'parent'            => 'root',
            'jenis_trans'       =>  $key
          );

          $jenis_transaksi=$this->jenis_transaksimodel->show()->data;
          $data_anggaran->$value=$jenis_transaksi;
          $i=0;
          //cari member jenis transaksi
          foreach ($jenis_transaksi as $transaksi) {
            $this->jenis_transaksimodel->filter='id,nm_jenis_trans,nominal,keterangan,extra';

              $this->jenis_transaksimodel->find=array(
                'parent'            => $transaksi->id
              );
              //memanggil array member transaksi
              $j=0;
              foreach ($this->jenis_transaksimodel->show()->data as $data_member) {
                # code...
                $extra = $data_member->extra;
                $data_anggaran->$value[$i]->sub[$j]=$data_member;
                $data_anggaran->$value[$i]->sub[$j]->debet=json_decode($extra)->debet;
                $data_anggaran->$value[$i]->sub[$j]->kredit=json_decode($extra)->kredit;
                $j++;
              }
          //  $data->$value[$i]->sub=$this->jenis_transaksimodel->show()->data;

            $data_anggaran->$value[$i]->jml=$this->jenis_transaksimodel->sum();

            $i++;
          }

        }

            return $this->rencana_anggaranmodel->show()->data;
  }

  function cari($args,$data,$userdata){

          $this->jenis_transaksimodel->dbname = 'KSAAS_'.$userdata->user_group;

    //get rencana anggaran where id defined = params
          $this->rencana_anggaranmodel->find = array(
              'tahun_anggaran' => $args['tapel'],
              'status'         => '1'
          );
          $data= $this->rencana_anggaranmodel->show()->data;
          $output=array();
          foreach ($data as $ra) {


              //filter kolom
              $this->jenis_transaksimodel->filter='id,nm_jenis_trans';
              //get member of selected rencana anggaran
              $this->jenis_transaksimodel->find=array(
                'rencana_anggaran'  => $ra->id,
                'parent'            => 'root',
                'jenis_trans'       =>  $args['jenis']
              );
              $jenis_transaksi=$this->jenis_transaksimodel->show()->data;
              $output=$jenis_transaksi;
              $i=0;

              foreach ($jenis_transaksi as $transaksi) {
                $this->jenis_transaksimodel->filter='id,nm_jenis_trans,nominal,keterangan,extra';

                  $this->jenis_transaksimodel->find=array(
                    'parent'            => $transaksi->id
                  );
                $output[$i]->sub=$this->jenis_transaksimodel->show()->data;
                $i++;
              }

          }

          return $output;
  }


  function add($args,$data,$userdata){
        $this->rencana_anggaranmodel->dbname= 'KSAAS_'.$userdata->user_group;

        $request_data = $data;
        $this->rencana_anggaranmodel->id             = 'RA'.substr(uniqid(mt_rand(), true), 0, 8);
        $this->rencana_anggaranmodel->nm_anggaran    = $request_data['nm_anggaran'];
        $this->rencana_anggaranmodel->tahun_anggaran = $request_data['tahun_anggaran'];
        $this->rencana_anggaranmodel->pencatat       = $request_data['pencatat'];
        $this->rencana_anggaranmodel->add();

        return array('status'=>'ok','id'=>$this->rencana_anggaranmodel->id);
  }

  function update($args,$data,$userdata){


        $this->rencana_anggaranmodel->find = array(
            'id' => $args['id_anggaran']
        );
        $this->rencana_anggaranmodel->update($data);

        return array('status'=>'ok');
  }

  function delete($args,$data,$userdata){


    $this->rencana_anggaranmodel->id= $args['id_anggaran'];

    $this->rencana_anggaranmodel->delete();
    return array('status'=>'ok');
  }

}
