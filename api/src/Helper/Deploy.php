<?php
namespace App\Helper;

class Deploy{
  public $dbname;
  function __construct(){
    $this->db= new Connection();
    $this->blueprint = file_get_contents(__DIR__ . "/../../database/blue_print.json");
  }

  function create_db(){
      $this->db->execute("create database ".$this->dbname);
  }

  function create_tables(){
     $this->db->conf['db_name']=$this->dbname;
      $data=json_decode($this->blueprint,true);
      foreach ($data as $table) {
          $q='';
          $q .= "create table ".$table['name']." (";
          $i=0;
            foreach ($table['field'] as $field) {
              $i++;
               $q .=$field['field_name'].' '.$field['datatype'].' ';
               if(isset($field['prop']))
               {
                 $q .=$field['prop'];
               }
              if ($i==count($table['field'])){

              }
              else{
                $q .= ",";
              }

            }
          $q .=', PRIMARY KEY ('.$table['primary_key'].') )';
          $this->db->execute($q);
          if(isset($table['value'])){
            foreach ($table['value'] as $isi) {
              $this->db->insert($table['name'],$isi);
            }
          }
      }
  }

  function haha(){
     $data=json_decode($this->blueprint);
     return $data;
  }

}
