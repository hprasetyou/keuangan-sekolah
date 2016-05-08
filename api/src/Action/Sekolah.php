<?php
namespace App\Action;


class Sekolah{

  public function __construct(){
    $this->sekolahmodel= new \App\Model\Sekolah();
    $this->usermodel = new  \App\Model\User();
  }

  public function tampil($args){
          if (array_key_exists('cond',$args)){
              $all_cond=explode('&',$args['cond']);
              $newcondition= array();
              foreach ($all_cond as $cond) {
                 $condition=explode('=',$cond);
                 $newcondition[$condition[0]]=$condition[1];
              }
                $this->sekolahmodel->find = $newcondition;
              }

          return $this->sekolahmodel->show()->data;
  }

  public function update($args,$data){
    $data['createat']=date('Y-m-d');
    $this->sekolahmodel->update($args['id'],$data);

    if(isset($data['status'])=='1'){
      $this->usermodel->find= array('user_group'=>$args['id']);
      foreach ($this->usermodel->show()->data as $userdata) {
        # code...
        $setting = \App\Helper\Setting::get();
        $mail_subject='Halo '.$userdata->email;
        $mail_body = array();
        $mail_body['msgtitle'] = 'Halo ';
        $mail_body['msgbody']='<h2>Selamat, pendaftaran anda telah disetujui</h2> <p>Anda bisa mulai menggunakan aplikasi pengelolaan keuangan sekolah dengan login ke <a href="'.$setting['root'].'">'.$setting['root'].'</a> </p><br>
        <p>Inputkan <b></b> dan password yang talah anda setel<br>
        Terima kasih';
        $mail_recipient = $userdata->email;
        \App\Helper\Mailer::send($mail_subject,$mail_body,$mail_recipient);

      }
    }

        return array('status'=>'ok');
  }

};
