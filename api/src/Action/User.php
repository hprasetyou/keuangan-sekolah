<?php
namespace App\Action;

class User{

        private $usermodel;
        public function __construct(){
          $this->usermodel = new \App\Model\User();
          $this->sekolahmodel = new \App\Model\Sekolah();
        }

        public function tampil($args){

          if (array_key_exists('cond',$args)){
                    $all_cond=explode('&',$args['cond']);
                    $newcondition= array();
                    foreach ($all_cond as $cond) {
                       $condition=explode('=',$cond);
                       $newcondition[$condition[0]]=$condition[1];
                    }
                    $this->usermodel->find = $newcondition;
                  }
          return $this->usermodel->show();
        }

      public function update($args,$data){

          foreach ($data as $key => $value) {
              $newdata[$key]= $value;
          }
          $this->usermodel->update($args['user_id'],$newdata);

          return array('message'=>'ok');
      }

      public function delete($args){
          $this->usermodel->user_id=$args['user_id'];
          $this->usermodel->delete();
          return array('status'=>'success','message'=>'data terhapus');
      }

      function login($args,$data){

      $this->usermodel->find=array(
        'email'=>$data['email'],
        'status'=>'1'
      );

      $cek_in=$this->usermodel->detail();
      if($cek_in->num_rows<1){
        //login gagal
        $output=array('auth'=>0,
                      'msg'=>'username tidak ditemukan'
                    );
      }
      else {
        $data_user=$cek_in->data[0];
        $this->sekolahmodel->find=array('group_id'=>$data_user->user_group);
        $data_sekolah=$this->sekolahmodel->detail();
        $data_output['user_id']=$data_user->user_id;
        $data_output['user_email']=$data_user->email;
        $data_output['user_group']=$data_user->user_group;
        $data_output['user_level']=$data_user->user_level;
        $data_output['user_privilege']=$data_user->privilege;

        //check password
        if (password_verify($data['password'], $data_user->password))
              {
                $output=array('auth'=>1,
                              'token'=> \App\Helper\Jwt::encode($data_output)
                            );
              }
        else
              {
                $output=array('auth'=>0,
                              'msg'=>'Password Salah'
                            );
              }
      }
      return $output;
    }



}
