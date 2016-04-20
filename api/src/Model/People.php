<?php
namespace App\Model;

class People{
public $id;
public $nama;
public $info_lain;
public $kelompok;
public $find='';


    public function __construct($connection){
      $this->db = $connection;
    }

    public function add()
    {
      date_default_timezone_set('Asia/Jakarta');
      $date = date('Y-m-d H:i:s', time());

      $this->db->conf['db_name']=$this->dbname;
      $q=$this->db->insert('people',
      array(
          'id'        =>$this->id,
          'nama'      =>$this->nama,
          'createat'  =>$date,
          'info_lain' =>$this->info_lain,
          'kelompok'  =>$this->kelompok
      ));
    }

    public function show(){
      $this->db->conf['db_name']=$this->dbname;
      $condition='';
      $find=$this->find;
      if($this->find=='')
      {
        $condition .="NOT kelompok = 'u' AND ";
      }
      else
      {
          foreach ($this->find as $key => $value) {
            $condition .= $key."='".$value."' AND ";
          }
      }
      $condition .= '1';

      $q = $this->db->execute('select id, nama, createat, info_lain, kelompok from people where '.$condition);
      return $q;
    }


}
