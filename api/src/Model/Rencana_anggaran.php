<?php
namespace App\Model;

class Rencana_anggaran{

    public $dbname='';
    public $id;
    public $nm_anggaran;
    public $tahun_anggaran;
    public $pencatat;
    public $find=array();

    function __construct(){
        $this->db = new \App\Helper\Connection();
        $this->db->conf['db_name']="KSAAS_". \App\Helper\Auth::user_data()->user_group;
    }

    function show(){

        $condition='';
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }
        $condition .= '1';
        return $this->db->execute('select * from rencana_anggaran where '.$condition.' ORDER BY tahun_anggaran desc');
    }

    function update($data){
      $condition='';
      foreach ($this->find as $key => $value) {
        $condition .= $key."='".$value."' AND ";
      }
      $condition .= '1';
      $this->db->condition=$condition;
      return $this->db->update('rencana_anggaran',$data);
    }
    function add(){
      $data=array(
        'id'            =>$this->id,
        'nm_anggaran'   =>$this->nm_anggaran,
        'tahun_anggaran'=>$this->tahun_anggaran,
        'pencatat'      =>$this->pencatat
      );
        $this->db->insert('rencana_anggaran',$data);
    }

    function delete(){

        $this->db->condition="id='".$this->id."'";
        return $this->db->delete('rencana_anggaran');
    }


}
