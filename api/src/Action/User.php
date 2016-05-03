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
            if($key=='password'){
              $value =   $pass= hash('ripemd160', $value);
            }
            else{

            }
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

        $pass= hash('ripemd160', $data['password']);
        //check password
        if ($pass == $data_user->password)
              {
                $output=array('auth'=>1,
                              'token'=> \App\Helper\Auth::set_token($data_output)
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


    function verifikasi($args)
        {
          try{
            $token_data= \App\Helper\Jwt::decode($args['token']);

            if ((time())<=$token_data->val_time){
                  $this->usermodel->update($token_data->user_id,array('status'=>'1'));
                  $output=array('msg'=>'success');
              }
              else{
                  $output=array('msg'=>'token_expired');
              }
              return $output;
          }
          catch(Exception $e){
            return array('error'=>'invalid token');
          }
      }






    function add($args,$data){

      $this->sekolahmodel->find = array('group_id' => $data['user_group']);
      $group=$this->sekolahmodel->detail();

      if($group->num_rows<1)
        {

          $this->sekolahmodel->group_id  = $data['user_group'];
          $this->sekolahmodel->group_name  = $data['group_name'];
          $this->sekolahmodel->create();

        }
        else{

        }

            $password="kosong";

            $this->usermodel->user_group   = $data['user_group'];


            $this->usermodel->user_id          =$data['user_id'];
            $this->usermodel->email            =$data['email'];

            $this->usermodel->password         =$password;

            $this->usermodel->create();

            //setup validation token
            $ver_token=\App\Helper\Jwt::encode(array(
              'user_id'    => $data['user_id'],
              'val_time'    => time()+3600
            ));
            $mail_subject='Halo '.$data['email'];

            $mail_body ='Silahkan verifikasi email anda, salin dan tempel token dibawah ini untuk memverifikasi akun anda <br>
          <br><br>'.$ver_token;
            $mail_recipient = $data['email'];
            \App\Helper\Mailer::send($mail_subject,$mail_body,$mail_recipient);

        return array('status'=>'ok');
    }



}
