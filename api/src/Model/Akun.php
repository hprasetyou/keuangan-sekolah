<?php
namespace App\Model;



class Akun {
public $id_akun;
public $nama_akun;
public $jenis_akun;
public $keterangan;

public $find;
public $db;

  public function __construct(){
    $this->db = new \App\Helper\Connection();
  }

  public function show()
  {
      $this->db->conf['db_name']=$this->dbname;
      $condition='';
      if (is_array($this->find)){
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }
      }
      $condition .= '1';
      $q=$this->db->execute("select `id_akun`, `nama_akun`,  `jenis_akun`, `keterangan` from akun where ".$condition);
      return $q;
  }
  public function update($data)
  {
    $this->db->conf['db_name']=$this->dbname;
    $condition='';
    if (is_array($this->find)){
      foreach ($this->find as $key => $value) {
        $condition .= $key."='".$value."' AND ";
      }
    }
    $condition .= '1';
//    return $this->db->update('akun',$data);
    $sql= "update akun set ";
    $i=1;
    foreach ($data as $key => $value) {
      $sql .= $key." = '".$value."' ";
      if($i<count($data)){
        $sql .= ',';
      }
      $i++;
    }
    $this->db->execute($sql." where ".$condition);
  }
  function add(){
    $this->db->conf['db_name']=$this->dbname;
    $this->db->insert('akun',array(
        'id_akun'     =>$this->id_akun,
        'nama_akun'   =>$this->nama_akun,
        'jenis_akun'  =>$this->jenis_akun
      )
    );
  }
  function delete(){
      $this->db->conf['db_name']=$this->dbname;
      $condition='';
      if (is_array($this->find)){
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }
      }
      $condition .= '1';
      $this->db->execute("delete from akun where ".$condition);
  }

}
