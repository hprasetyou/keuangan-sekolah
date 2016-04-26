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
}

  public function add(){
    $this->db->conf['db_name']=$this->dbname;
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
