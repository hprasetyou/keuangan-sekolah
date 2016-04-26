<?php
namespace App\Model;

class Jenis_transaksi{
    public $dbname;
    public $find;
    public $filter='*';

    public $id;
    public $nm_jenis_trans;
    public $rencana_anggaran;
    public $parent;
    public $jenis_trans;
    public $nominal;
    public $extra;
    public $keterangan;



    public function __construct(){
      $this->db = new \App\Helper\Connection();
  }

    public function add(){
        $this->db->conf['db_name']=$this->dbname;
        $q=$this->db->insert('jenis_transaksi',
        array(
          'id'                => $this->id,
          'nm_jenis_trans'    => $this->nm_jenis_trans,
          'rencana_anggaran'  => $this->rencana_anggaran,
          'parent'            => $this->parent,
          'jenis_trans'       => $this->jenis_trans,
          'nominal'           => $this->nominal,
          'extra'             => $this->extra,
          'keterangan'        => $this->keterangan
      ));

    }
    public function show(){
        $this->db->conf['db_name']=$this->dbname;
        $condition='';
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }
        $condition .= '1';
        return $this->db->execute('select '.$this->filter.' from jenis_transaksi where '.$condition);

    }
    public function sum(){
      $this->db->conf['db_name']=$this->dbname;
      $condition='';
      foreach ($this->find as $key => $value) {
        $condition .= $key."='".$value."' AND ";
      }
      $condition .= '1';
      $q= $this->db->execute('select sum(nominal) as jml_nominal from jenis_transaksi where '.$condition);
      return $q->data[0]->jml_nominal;
    }

    public function delete(){
        $this->db->conf['db_name']=$this->dbname;
        $condition='';
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }
        $condition .= '1';
        return $this->db->execute('delete from jenis_transaksi where '.$condition);

    }

}
