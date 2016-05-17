<?php
namespace App\Model;

class Transaksi{
  public $db;
  public $dbname;
  public $find;


  public $id_transaksi;
  public $id_jenis_transaksi;
  public $pencatat;
  public $uraian;

  public function __construct(){
    $this->db = new \App\Helper\Connection();
    $this->db->conf['db_name']="KSAAS_". \App\Helper\Auth::user_data()->user_group;
}

  public function add(){

    date_default_timezone_set('Asia/Jakarta');
    $date = date('Y-m-d H:i:s', time());
    $this->db->insert('tb_transaksi',array(
      'id'                =>$this->id_transaksi,
      'id_jenis_transaksi'=>$this->id_jenis_transaksi,
      'waktu'             =>$date,
      'pencatat'          =>$this->pencatat,
      'uraian'           =>$this->uraian
    ));
  }

  public function sum(){
    $this->find['akun']='001';
    $condition='';
    foreach ($this->find as $key => $value) {
      $condition .= $key."='".$value."' AND ";
    }
    $condition .= '1';
    $q=$this->db->execute("SELECT case when debet is null then 0 else sum(debet)+sum(kredit) end as jumlah
    FROM `tb_transaksi` JOIN jurnal on tb_transaksi.id=jurnal.id_transaksi
    where ".$condition);
    return $q;
  }

  public function sum_lain_lain($jenis){
    if($jenis=='m'){
      $cond = "kredit=0";
    }else{
      $cond = "debet=0";
    }
    $q=$this->db->execute("SELECT tb_transaksi.id, uraian as nm_jenis_trans, debet+kredit as realisasi FROM `tb_transaksi` left JOIN jurnal on tb_transaksi.id = jurnal.id_transaksi where tb_transaksi.id_jenis_transaksi='undefined'
    and akun = '001'
    and ".$cond."
    and tb_transaksi.id not in (select id_transaksi from jurnal WHERE akun='600')
    and date_format(waktu,'%Y-%m-%d') between '".$this->find['mulai']."' and '".$this->find['akhir']."'
    ");
    return $q;
  }


}
