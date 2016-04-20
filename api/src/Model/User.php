<?php
namespace App\Model;


class User{

    private $db;
    public $user_id;
    public $email;
    public $password;
    public $user_group;
    public $user_level;
    public $privilege;
    public $find;

    public function __construct() {
        $this->db = new \App\Helper\Connection();
    }
    public function show(){
      $condition='';
      if(isset($this->find)){
      foreach ($this->find as $key => $value) {
        $condition .= $key."='".$value."' AND ";
      }}
      $condition .= '1';
      $q=$this->db->execute("select `user_id`, `email`,  `user_group`, `create_at`, `user_level`, `privilege`, `status` from user where ".$condition);
      return $q;
    }
    public function detail(){
      $condition='';
      foreach ($this->find as $key => $value) {
        $condition .= $key."='".$value."' AND ";
      }
      $condition .= '1';
      $q=$this->db->execute("select `user_id`,password, `email`,  `user_group`, `create_at`, `user_level`, `privilege`, `status` from user where ".$condition);
      return $q;
    }





  public function update($id,$data)
  {
    $this->db->condition="user_id='".$id."'";
    return $this->db->update('user',$data);
  }

  public function delete()
  {
    $this->db->condition="user_id='".$this->user_id."'";
    return $this->db->delete('user');
  }


  //create new user
  public function create()
  {
    date_default_timezone_set('Asia/Jakarta');
    $date = date('Y-m-d H:i:s', time());
    $q=$this->db->insert('user',array(
      'user_id'    =>$this->user_id,
      'email'      =>$this->email,
      'password'   =>$this->password,
      'user_group' =>$this->user_group,
      'create_at'  =>$date,
      'status'     =>0,
      'privilege' =>'11111',
      'user_level' =>'2')
    );
    return $q;
  }

}
