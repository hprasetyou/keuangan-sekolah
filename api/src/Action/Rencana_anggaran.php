<?php
namespace App\Action;

class Rencana_anggaran{

  function __construct(){
        $this->rencana_anggaranmodel= new \App\Model\Rencana_anggaran();
        $this->jenis_transaksimodel= new \App\Model\Jenis_transaksi();
        $this->transaksimodel= new \App\Model\Transaksi();
  }

  public function tampil($args,$data,$userdata){
    if (array_key_exists('cond',$args)){
              $all_cond=explode('&',$args['cond']);
              $newcondition= array();
              foreach ($all_cond as $cond) {
                 $condition=explode('=',$cond);
                 $newcondition[$condition[0]]=$condition[1];
              }
             $this->rencana_anggaranmodel->find = $newcondition;
            }
          return $this->rencana_anggaranmodel->show()->data;
  }


  function detail($args,$data,$userdata){
    $realisasi = 0;
    $jml=0;

          $this->jenis_transaksimodel->dbname = 'KSAAS_'.$userdata->user_group;

          //get rencana anggaran where id defined = params
          $this->rencana_anggaranmodel->find = array(
              'id' => $args['id']
          );
          $data_anggaran = $this->rencana_anggaranmodel->show()->data[0];

          $jenis_trans= array('m'=>'jenis_trans_masuk','k'=>'jenis_trans_keluar');
        //jenis_trans_masuk
        //================================================


                  //filter kolom
                  $this->jenis_transaksimodel->filter='id,nm_jenis_trans';
                  //get member of selected rencana anggaran
                  //jenis transaksi kategori root
                  $this->jenis_transaksimodel->find=array(
                    'rencana_anggaran'  => $args['id'],
                    'parent'            => 'root',
                    'jenis_trans'       =>  'm'
                  );

                  $jenis_transaksi=$this->jenis_transaksimodel->show()->data;
                  $data_anggaran->jenis_trans_masuk=$jenis_transaksi;
                  $i=0;
                  //cari member jenis transaksi
                  foreach ($jenis_transaksi as $transaksi) {
                    $this->jenis_transaksimodel->filter='id,nm_jenis_trans,nominal,keterangan,extra';

                      $this->jenis_transaksimodel->find=array(
                        'parent'            => $transaksi->id
                      );
                      //memanggil array member transaksi
                      $j=0;
                      $realisasi = 0;
                      foreach ($this->jenis_transaksimodel->show()->data as $data_member) {
                        # code...
                        $extra = $data_member->extra;
                        $data_anggaran->jenis_trans_masuk[$i]->sub[$j]=$data_member;
                        $data_anggaran->jenis_trans_masuk[$i]->sub[$j]->debet=json_decode($extra)->debet;
                        $data_anggaran->jenis_trans_masuk[$i]->sub[$j]->kredit=json_decode($extra)->kredit;

                        //realisasi
                        $this->transaksimodel->find= array('id_jenis_transaksi'=>$data_member->id);
                        $jml= $this->transaksimodel->sum()->data[0]->jumlah;
                        $data_anggaran->jenis_trans_masuk[$i]->sub[$j]->realisasi=$jml;

                        $realisasi = $realisasi +$jml;
                        $j++;
                      }
                      //realisasi


                  //  $data->$value[$i]->sub=$this->jenis_transaksimodel->show()->data;

                    $data_anggaran->jenis_trans_masuk[$i]->jml=$this->jenis_transaksimodel->sum();
                    $data_anggaran->jenis_trans_masuk[$i]->realisasi= $realisasi;
                    $i++;
                  }
                  //end foreach loop jenis transaksi

                  //realisasi lain lain
                  $lain_masuk = new \stdClass;
                  $lain_masuk->id = "0";
                  $lain_masuk->nm_jenis_trans= "Lain lain";

                  $lain_masuk->jml=0;
                  $lain_masuk->realisasi=0;
                  $this->transaksimodel->find= array(
                    'mulai'=>'20'.substr($this->rencana_anggaranmodel->show()->data[0]->tahun_anggaran,0,2).'-07-01',
                    'akhir'=>'20'.substr($this->rencana_anggaranmodel->show()->data[0]->tahun_anggaran,2,2).'-06-31');
                  $q_lain_masuk =$this->transaksimodel->sum_lain_lain('m');
                  $lain_masuk->sub = $q_lain_masuk->data;
                  foreach ($q_lain_masuk->data as $data_lain_masuk) {
                    $lain_masuk->realisasi+= $data_lain_masuk->realisasi;
                    # code...
                  }
                  //tampilkan jika ada
                  if($q_lain_masuk->num_rows > 0 and $args['realisasi']=='1'){
                      $data_anggaran->jenis_trans_masuk[$i]=$lain_masuk;
                  }

                  //end


                  $realisasi = $realisasi +$jml;
                  //====================

                  //jenis_trans_keluar
                  //================================================


                            //filter kolom
                            $this->jenis_transaksimodel->filter='id,nm_jenis_trans';
                            //get member of selected rencana anggaran
                            //jenis transaksi kategori root
                            $this->jenis_transaksimodel->find=array(
                              'rencana_anggaran'  => $args['id'],
                              'parent'            => 'root',
                              'jenis_trans'       =>  'k'
                            );

                            $jenis_transaksi=$this->jenis_transaksimodel->show()->data;
                            $data_anggaran->jenis_trans_keluar=$jenis_transaksi;
                            $i=0;
                            //cari member jenis transaksi
                            foreach ($jenis_transaksi as $transaksi) {
                              $this->jenis_transaksimodel->filter='id,nm_jenis_trans,nominal,keterangan,extra';

                                $this->jenis_transaksimodel->find=array(
                                  'parent'            => $transaksi->id
                                );
                                //memanggil array member transaksi
                                $j=0;
                                $realisasi = 0;
                                foreach ($this->jenis_transaksimodel->show()->data as $data_member) {
                                  # code...
                                  $extra = $data_member->extra;
                                  $data_anggaran->jenis_trans_keluar[$i]->sub[$j]=$data_member;
                                  $data_anggaran->jenis_trans_keluar[$i]->sub[$j]->debet=json_decode($extra)->debet;
                                  $data_anggaran->jenis_trans_keluar[$i]->sub[$j]->kredit=json_decode($extra)->kredit;

                                  $this->transaksimodel->find= array('id_jenis_transaksi'=>$data_member->id);
                                  $jml= $this->transaksimodel->sum()->data[0]->jumlah;
                                  $data_anggaran->jenis_trans_keluar[$i]->sub[$j]->realisasi=$jml;


                                  $realisasi = $realisasi +$jml;
                                  $j++;
                                }
                            //  $data->$value[$i]->sub=$this->jenis_transaksimodel->show()->data;

                              $data_anggaran->jenis_trans_keluar[$i]->jml=$this->jenis_transaksimodel->sum();
                              $data_anggaran->jenis_trans_keluar[$i]->realisasi= $realisasi;
                              $i++;
                            }
                            //====================
                            //realisasi lain lain
                            $lain_keluar = new \stdClass;
                            $lain_keluar->id = "0";
                            $lain_keluar->nm_jenis_trans= "Lain lain";

                            $lain_keluar->jml=0;
                            $lain_keluar->realisasi=0;
                            $this->transaksimodel->find= array(
                              'mulai'=>'20'.substr($this->rencana_anggaranmodel->show()->data[0]->tahun_anggaran,0,2).'-07-01',
                              'akhir'=>'20'.substr($this->rencana_anggaranmodel->show()->data[0]->tahun_anggaran,2,2).'-06-31');
                            $q_lain_keluar =$this->transaksimodel->sum_lain_lain('k');
                            $lain_keluar->sub = $q_lain_keluar->data;
                            foreach ($q_lain_keluar->data as $data_lain_keluar) {
                              $lain_keluar->realisasi+= $data_lain_keluar->realisasi;
                              # code...
                            }
                            //tampilkan jika ada
                            if($q_lain_keluar->num_rows > 0 and $args['realisasi']=='1'){
                                $data_anggaran->jenis_trans_keluar[$i]=$lain_keluar;
                            }

                            //end

            return $data_anggaran;
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
