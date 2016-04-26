<?php
namespace App\Model;

class Sekolah{

    public $group_id;
    public $group_name;
    public $find;

    public function __construct() {
        $this->db = new \App\Helper\Connection();
    }

    function show(){
        $condition='';
        if(isset($this->find)){
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }}
        $condition .= '1';
        return $this->db->execute("select * from sekolah where ".$condition);
      }

      public function detail(){
        $condition='';
        foreach ($this->find as $key => $value) {
          $condition .= $key."='".$value."' AND ";
        }
        $condition .= '1';
        $q=$this->db->execute("select * from sekolah where ".$condition);
        return $q;
      }


    function update($id,$data)
    {
        $this->db->condition="group_id='".$id."'";
        return $this->db->update('sekolah',$data);
    }



    function create(){
        $this->db->insert('sekolah',array(
            'group_id'    => $this->group_id,
            'group_name'  => $this->group_name,
            'status'      =>  0,
            'max_user'    =>  '3',
            'createat'    =>'now()'
          )
        );
    }

}
