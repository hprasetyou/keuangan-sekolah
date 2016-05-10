<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE); ini_set('display_errors','On');
error_reporting(0);
require 'vendor/autoload.php';

use Phroute\Phroute\RouteCollector;
$setting = \App\Helper\Setting::get();

$data = json_decode(file_get_contents('php://input'), true);



//call action

//deploy!!!!!
$deploy= new \App\Helper\Deploy();



  $useraction = new \App\Action\User();
  $sekolahaction = new \App\Action\Sekolah();

$path= $setting['path'];

$router = new RouteCollector();

$router->filter('auth', function(){
  $userdata= \App\Helper\Auth::user_data();
  if($userdata->auth==1){

    $akunaction = new \App\Action\Akun();


  }
  else{
    return $userdata;
  }
});



//==============================================================================
//================================_USER_ROUTE_==================================
//==============================================================================
$router->get($path.'/user', function() use($useraction){
    $args= array ();
    return $useraction->tampil($args);
});


$router->post($path.'/cek-email', function() use($useraction,$data){
    $args= array ('cond'=>'email='.$data['email']);
    $cek = $useraction->tampil($args)->num_rows;
    if($cek<1){
      //login gagal
      $output=array('status'=>'ok');
    }
    else{
      $output=array('status'=>'error','msg'=>'email sudah digunakan');
    }
    return $output;
});


$router->post($path.'/user', function() use($useraction,$data){
    $args= array ();
    return $useraction->add($args,$data);
});
$router->get($path.'/user/verif/{token}', function($token) use($useraction,$data){
    $args= array ('token'=>$token);
    return $useraction->verifikasi($args,$data);
});

$router->get($path.'/user/{cond}/filter', function($cond) use($useraction){
  $args= array ('cond'=>$cond);
    return $useraction->tampil($args);
});

$router->get($path.'/user/{id}', function($id) use($useraction){
  $args= array ('cond'=>'user_id='.$id);
    return $useraction->tampil($args)->data[0];
});

$router->put($path.'/user/{id}', function($id) use($useraction,$data){
  $args= array ('user_id'=>$id);
    return $useraction->update($args,$data);
});

$router->delete($path.'/user/{id}', function($id) use($useraction,$data){
  $args= array ('user_id'=>$id);
    return $useraction->delete($args,$data);
});



//==============================================================================
//==============================_SEKOLAH_ROUTE_=================================
//==============================================================================

$router->get($path.'/sekolah', function() use($sekolahaction){
    $args= array ();
    return $sekolahaction->tampil($args);
});

$router->get($path.'/sekolah/{cond}/filter', function($cond) use($sekolahaction){
    $args= array ('cond'=>$cond);
    return $sekolahaction->tampil($args);
});

$router->get($path.'/sekolah/{id}',function($id) use($sekolahaction){
    $args= array ('cond'=>'group_id='.$id);
    return $sekolahaction->tampil($args)[0];
});

$router->put($path.'/sekolah/{id}', function($id) use($sekolahaction,$data){
    $args= array ('id'=>$id);
    return $sekolahaction->update($args,$data);
});

$router->post($path.'/sekolah/{id}/create_db', function($id) use($deploy){
    $deploy->dbname='KSAAS_'.$id;
    $deploy->create_db();
    return array('status'=>'ok');
});


$router->post($path.'/sekolah/{id}/create_tables', function($id) use($deploy){
    $deploy->dbname='KSAAS_'.$id;
    $deploy->create_tables();
    return array('status'=>'ok');
});



//==============================================================================
//==========================_RENCANA-ANGGARAN_ROUTE_============================
//==============================================================================
$router->get($path.'/rencana_anggaran', function(){
    $rencana_anggaranaction = new \App\Action\Rencana_anggaran();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    return  array(
      'response'=>$rencana_anggaranaction->tampil('','',$userdata),
      'token'=>$newtoken
    );
},['before' => 'auth']);

$router->get($path.'/rencana_anggaran/{id}/detail', function($id){
    $rencana_anggaranaction = new \App\Action\Rencana_anggaran();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
  return  array(
    'response'=>$rencana_anggaranaction->detail(array('id'=>$id),'',$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);

$router->get($path.'/rencana_anggaran/tapel={tapel}&jenis={jenis}', function($tapel,$jenis){
    $rencana_anggaranaction = new \App\Action\Rencana_anggaran();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
  return  array(
    'response'=>$rencana_anggaranaction->cari(array('tapel'=>$tapel,'jenis'=>$jenis),'',$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);

$router->post($path.'/rencana_anggaran', function() use($data){
    $rencana_anggaranaction = new \App\Action\Rencana_anggaran();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    \App\Helper\Log::add($userdata->user_id,'membuat rencana anggaran baru');
  return  array(
    'response'=>$rencana_anggaranaction->add(array(),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->post($path.'/rencana_anggaran/{id_anggaran}', function($id_anggaran) use($data){
    $jenis_transaksiaction = new \App\Action\Jenis_transaksi();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    \App\Helper\Log::add($userdata->user_id,'menambahkan rencana kegiatan/anggaran ke rencana anggaran');
  return  array(
    'response'=>$jenis_transaksiaction->add(array('id_anggaran'=>$id_anggaran),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->put($path.'/rencana_anggaran/{id_anggaran}', function($id_anggaran) use($data){
    $rencana_anggaranaction = new \App\Action\Rencana_anggaran();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
  return  array(
    'response'=>$rencana_anggaranaction->update(array('id_anggaran'=>$id_anggaran),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->delete($path.'/rencana_anggaran/{id_anggaran}', function($id_anggaran) use($data){
    $rencana_anggaranaction = new \App\Action\Rencana_anggaran();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    \App\Helper\Log::add($userdata->user_id,'menghapus rencana anggaran');

  return  array(
    'response'=>$rencana_anggaranaction->delete(array('id_anggaran'=>$id_anggaran),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->delete($path.'/rencana_anggaran/{id_anggaran}/{id_jenis}', function($id_anggaran,$id_jenis) use($data){
    $userdata= \App\Helper\Auth::user_data();
      $jenis_transaksiaction = new \App\Action\Jenis_transaksi();
      \App\Helper\Log::add($userdata->user_id,'menghapus rencana kegiatan/anggaran dari rencana anggaran');

    $newtoken = \App\Helper\Auth::reset_timeout();
  return  array(
    'response'=>$jenis_transaksiaction->delete(array('id_anggaran'=>$id_anggaran,'id_jenis'=>$id_jenis),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);





//==============================================================================
//==============================_TRANSAKSI_ROUTE_===============================
//==============================================================================

$router->post($path.'/transaksi/{id_jenis_transaksi}', function($id_jenis_transaksi) use($data){
  $transaksiaction = new \App\Action\Transaksi();
    $userdata= \App\Helper\Auth::user_data();
    \App\Helper\Log::add($userdata->user_id,'menambahkan transaksi');

    $newtoken = \App\Helper\Auth::reset_timeout();
  return  array(
    'response'=>$transaksiaction->add(array('id_jenis_transaksi'=>$id_jenis_transaksi),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);







//==============================================================================
//==============================_BUKU_BESAR_ROUTE_==============================
//==============================================================================

$router->get($path.'/buku_besar/jurnal/{akun}/{tanggal_mulai}/{tanggal_akhir}', function($akun,$tanggal_mulai,$tanggal_akhir)
use($data){
  $buku_besaraction = new \App\Action\Buku_besar();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    $args = array('akun'=>$akun,'tanggal_mulai'=>$tanggal_mulai,'tanggal_akhir'=>$tanggal_akhir);
    return array(
    'response'=>$buku_besaraction->show($args,$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->get($path.'/buku_besar/jurnal/{akun}/{tanggal_mulai}/{tanggal_akhir}/{start}-{length}',
function($akun,$tanggal_mulai,$tanggal_akhir,$start,$length)
use($data){
  $buku_besaraction = new \App\Action\Buku_besar();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    $args =
    array('akun'=>$akun,
    'tanggal_mulai'=>$tanggal_mulai,
    'tanggal_akhir'=>$tanggal_akhir,
    'start'=>$start,
    'length'=>$length
  );
    return array(
    'response'=>$buku_besaraction->show($args,$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);

$router->get($path.'/buku_besar/saldo_per_jenis/{jenis}',
function($jenis)
use($data){
  $buku_besaraction = new \App\Action\Buku_besar();
    $userdata= \App\Helper\Auth::user_data();
    $newtoken = \App\Helper\Auth::reset_timeout();
    $args =
    array('jenis'=>$jenis,
    );
    return array(
      'response'=>$buku_besaraction->saldo_per_jenis($args,$data,$userdata),
      'token'=>$newtoken
  );
},['before' => 'auth']);


//==============================================================================
//=================================_AKUN_ROUTE_=================================
//==============================================================================
$router->get($path.'/akun', function()
use($data){
$akunaction = new \App\Action\Akun();
      $userdata= \App\Helper\Auth::user_data();
      $newtoken = \App\Helper\Auth::reset_timeout();
    return array(
    'response'=>$akunaction->show(array(),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->get($path.'/akun/{cond}', function($cond)
use($data){
$akunaction = new \App\Action\Akun();
      $userdata= \App\Helper\Auth::user_data();
      $newtoken = \App\Helper\Auth::reset_timeout();
    return array(
    'response'=>$akunaction->show(array('cond'=>$cond),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);




$router->post($path.'/akun', function()
use($data){
$akunaction = new \App\Action\Akun();
      $userdata= \App\Helper\Auth::user_data();
      $newtoken = \App\Helper\Auth::reset_timeout();
      \App\Helper\Log::add($userdata->user_id,'membuat akun baru');
    return array(
    'response'=>$akunaction->add(array(),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->put($path.'/akun/{id_akun}', function($id_akun)
use($data){
$akunaction = new \App\Action\Akun();
      $userdata= \App\Helper\Auth::user_data();
      $newtoken = \App\Helper\Auth::reset_timeout();
      \App\Helper\Log::add($userdata->user_id,'mengubah informasi akun');
    return array(
    'response'=>$akunaction->update(array('id_akun'=>$id_akun),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);



$router->delete($path.'/akun/{id_akun}', function($id_akun)
use($data){
$akunaction = new \App\Action\Akun();
      $userdata= \App\Helper\Auth::user_data();
      $newtoken = \App\Helper\Auth::reset_timeout();
      \App\Helper\Log::add($userdata->user_id,'menghapus akun');
    return array(
    'response'=>$akunaction->delete(array('id_akun'=>$id_akun),$data,$userdata),
    'token'=>$newtoken
  );
},['before' => 'auth']);



//
//==============================================================================
//==============================================================================


$router->get($path.'/neraca_lajur', function() use($data){
      $newtoken = \App\Helper\Auth::reset_timeout();
  $buku_besaraction = new \App\Action\Buku_besar();
    return array(
    'response'=> $buku_besaraction->get_neraca_lajur('','',''),
    'token'=>$newtoken
  );
},['before' => 'auth']);


$router->get($path.'/saldo', function() use($data){
  $newtoken = \App\Helper\Auth::reset_timeout();
  $buku_besaraction = new \App\Action\Buku_besar();
  return array(
    'response'=> $buku_besaraction->saldo_per_akun('','',''),
    'token'=>$newtoken
  );
},['before' => 'auth']);

//
//==============================================================================
//==============================================================================

$router->post($path.'/_session', function() use($useraction,$data){
    $args= array ();
    return $useraction->login($args,$data);
});

$router->get($path.'/_session', function(){

    $args= array ();
    try{
        $output= \App\Helper\Auth::user_data();
    }
    catch(Exception $e){
    $output = array('error'=>$e);
    }
    return  $output;
});

$router->post($path.'/_session/update', function(){
  $args= array ();
  $output= \App\Helper\Auth::reset_timeout();
  return  $output;
});

$router->get($path.'/log/{page}', function($page){
    $userdata= \App\Helper\Auth::user_data();
    return \App\Helper\Log::get($userdata->user_group,$page)->data;
});


//==============================================================
//+++++++++++==============END ROUTE ==============+++++++++++++
//==============================================================
$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

header('Content-Type: application/json');
// Print out the value returned from the dispatched function
//echo json_encode($response);

echo json_encode($response);
