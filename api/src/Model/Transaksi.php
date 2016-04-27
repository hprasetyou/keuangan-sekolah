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


}
