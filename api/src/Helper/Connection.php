<?php
namespace App\Helper;


use \PDO;
class Connection{

    public $conf;
    public $condition='1';

    public function __construct(){
      $this->conf['db_user']='root';
      $this->conf['db_pass']='0000000';
      $this->conf['db_name']='KSAAS_main';
      $this->conf['db_host']='localhost';
    }

      private function getConnection()
      {
        	$dbh = new \PDO("mysql:host=".$this->conf['db_host'].";dbname=".$this->conf['db_name'],  $this->conf['db_user'] , $this->conf['db_pass']);
        	$dbh->setAttribute(\PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        	return $dbh;
      }
      function insert($table,$data){
          $sql="INSERT INTO ".$table." (".$this->col_val($data,'key').") VALUES(".$this->col_val($data,'val').")";
          return $this->execute($sql);
      }

      function update($table,$data){
          $sql="UPDATE ".$table." set ";
          $i=0;
          foreach (array_keys($data) as $key){
            $i++;
            if($i==count($data))
            {
              $sql .= $key.'="'.$data[$key].'"';
            }
            else {
              $sql .= $key.'="'.$data[$key].'",';
            }
          }
          $sql .= ' where '.$this->condition;
          return $this->execute($sql);
      }

      function select($table,$column='*'){
          $sql="select $column from $table where ".$this->condition;
          return $this->execute($sql);
      }

      function delete($table)
      {
        $sql="delete from $table where ".$this->condition;
        return $this->execute($sql);
      }


      function col_val($data,$type)
      {
        $i=0;
        $d='';
        if($type=='key')
        {
          foreach (array_keys($data) as $key){
            $i++;
            if($i==count($data))
            {
              $d .= $key;
            }
            else {
              $d .= $key.',';
            }
          }
        }
        else {
          foreach ($data as $key){
            $i++;
            if($i==count($data))
            {
              $d .= "'$key'";
            }
            else {
              $d .= "'$key',";
            }
          }
        }
          return $d;
    }


    public function execute($query)
    {
      $output= new \stdClass();
      try
      {
          $db = $this->getConnection();
          $stmt = $db->query($query);
          if ((preg_match("/select/i",$query)) or (preg_match("/desc/i",$query)))
          {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);
          }
          else
          {
            $data="success";
          }
          $output->success  =true;
          $output->data     =$data;
          $output->num_rows = count($data);
          $output->query    =$query;
      } catch(PDOException $e)
      {
          $output->data = $e->getMessage();
          $output->success  =0;
          $output->query    =$query;
      }
      return $output;
    }
}
