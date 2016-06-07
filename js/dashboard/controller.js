

app.run(['$rootScope','Saldo','userdata','User','User_group','akun',
function($rootScope,Saldo,userdata,User,User_group,akun){
//userdata.cekAuth()
console.log(userdata.token);
$rootScope.theme = localStorage.getItem('theme');

    $rootScope.Jenis_akun=akun.Jenis
    $rootScope.tampil_saldo= function(){
    Saldo.Show().then(function(output){
      $rootScope.datasaldo=output
      for(var i=0;i<output.length;i++){
        if(output[i].jenis_akun=='a' || output[i].jenis_akun=='b'){
          $rootScope.datasaldo[i].saldo=output[i].debet-output[i].kredit
        }
        else{
          $rootScope.datasaldo[i].saldo=output[i].kredit-output[i].debet
        }
      }
      })
    }

      var today = new Date();
      var dd = today.getDate();
      var mm = today.getMonth()+1; //January is 0!
      var yyyy = today.getFullYear();

      if(dd<10) {
          dd='0'+dd
        }

        if(mm<10) {
          mm='0'+mm
        }


        $rootScope.Dt= {
          d:dd,
          m:mm,
          y:yyyy
        }


  userdata.Get().then(function(response){
  $rootScope.userdata=response
  $rootScope.userdata.privilege=$rootScope.userdata.user_privilege.split("");
  User.Detail({'id':response.user_id}).then(function(res){
    $rootScope.userdata.display_name = res.display_name
    $rootScope.userdata.phone = res.phone
    console.log(res);
    })

  User_group.Detail(response).then(function(group_res){
    $rootScope.userdata.sekolah = group_res[0].group_name;

  })
})
$rootScope.logout= function(){
  userdata.Logout()
}

$rootScope.alert=[];
$rootScope.addalert= function(tipe,msg){
  var no=$rootScope.alert.length;
  $rootScope.alert.push({'type':tipe,'msg':msg,'no':no})
  window.setTimeout(function () {
      $("#alert_"+no).alert('close');
      $rootScope.hapus_alert(no);
    }, 3000);
  }
$rootScope.hapus_alert=function(no){
  $rootScope.alert.splice(no, 1);
}

}])


app.controller('home',['$scope','Log','$rootScope','Saldo',
function($scope,Log,$rootScope,Saldo){
  var page = 1;
  Log.Show(page).then(function(res){
    $scope.log = res
  })
  $scope.more = function(){
    page = page+1;
    Log.Show(page).then(function(res){
      for(var i=0; i< res.length; i++){
          $scope.log.push(res[i]);
          console.log(res[i]);
      }
    })
  }
}])





//=========================================================================
//=========================================================================
//=========================================================================
//=========================================================================
app.controller('rencana-anggaran',['$scope','tapelService','ra','jenis_transaksi','$rootScope','akun','helper',
function($scope,tapelService,ra,jenis_transaksi,$rootScope,akun,helper){

    $scope.lsttapel=tapelService.tapel_2th
    $scope.tapel_pilih=tapelService.tapel_sekarang
    $scope.tapel_sekarang=tapelService.tapel_sekarang

//tampil rencana anggaran
    var tampil_ra= function(){
      ra.Get().then(function(response){
        $scope.ra=response;
        $scope.ra_pilih= response[0];
        $scope.edit_ra = false;
      })
    }
    tampil_ra();
//    tampil_ra();
    $scope.nmtapel=[]
    $scope.adatapel=[]
    for(var i=0; i<$scope.lsttapel.length; i++){
      ra.Tahun({tahun:$scope.lsttapel[i]}).then(function(response){
        $scope.adatapel.push(response.length+'');
        console.log($scope.adatapel[i] + ' / '+ $scope.lsttapel.length);
      })
      $scope.nmtapel[i]="20"+$scope.lsttapel[i].substr(0,2)+"/20"+$scope.lsttapel[i].substr(2,2)
    }
//tambahkan rencana anggaran
    $scope.add_ra= function(){
      $("#loading").modal('show');
      $scope.frm_tapel.pencatat=$rootScope.userdata.user_id

      ra.Add($scope.frm_tapel).then(function(response){
        $("#loading").modal('hide');
        $scope.ra.push($scope.frm_tapel)
        $rootScope.addalert('success','Rencana Anggaran dibuat');
        $scope.frm_tapel.id= response.id.substring(0, 5);
        $scope.ra_pilih=scope.frm_tapel;
      })
    }

    $scope.pilih = function(data){
      $scope.ra_pilih=data
    }

      //kelola sub jenis transaksi
        var subjenis=[{
        }]
        $scope.subjenis= subjenis
        $scope.tambah_uraian= function(){
          $scope.subjenis.push({})
        }
        $scope.hapus_uraian = function(pos){
          $scope.subjenis.splice(pos,1)
        }

      //tambah jenis transaksi
        $scope.add_jenis_trans= function(mk){
          $("#loading").modal('show');
          $scope.hapus_jenis_anggaran();
          var data = {
            nm_jenis_trans: $scope.ra_trans.nm_jenis_trans,
            sumber_dana: $scope.ra_trans.sumber_dana,
            ra: $scope.ra_pilih.id,
            debet: $scope.ra_trans.debet,
            kredit: $scope.ra_trans.kredit,
            jenis_trans: mk,
            sub: $scope.subjenis
          }
          jenis_transaksi.Add(data).then(function(response){
            if(mk == "k"){
              var jml_keluar = 0
              for(var i=0;i<$scope.subjenis.length;i++){
                $scope.detail_ra.jum_keluar += $scope.subjenis[i].nominal*1
                jml_keluar += $scope.subjenis[i].nominal*1

              }
                $scope.detail_ra.jenis_trans_keluar.push(
                  {
                    id: response.id,
                    nm_jenis_trans: $scope.ra_trans.nm_jenis_trans,
                    sumber_dana: $scope.ra_trans.sumber_dana,
                    debet: $scope.ra_trans.debet,
                    kredit: $scope.ra_trans.kredit,
                    jenis_trans: mk,
                    sub: $scope.subjenis,
                    jml: jml_keluar
                  }
                )
            }
            else{
              var jmlkeluar = 0
              for(var i=0;i<$scope.subjenis.length;i++){
                $scope.detail_ra.jum_masuk += $scope.subjenis[i].nominal*1
                jml_masuk += $scope.subjenis[i].nominal*1
              }
                $scope.detail_ra.jenis_trans_masuk.push({
                  id: response.id,
                  nm_jenis_trans: $scope.ra_trans.nm_jenis_trans,
                  sumber_dana: $scope.ra_trans.sumber_dana,
                  debet: $scope.ra_trans.debet,
                  kredit: $scope.ra_trans.kredit,
                  jenis_trans: mk,
                  sub: $scope.subjenis,
                  jml: jml_masuk
                })
            }
            $scope.ra_trans={}
            $scope.subjenis=[{}]
            $("#loading").modal('hide');
            $rootScope.addalert('success','Jenis transaksi ditambahkan');

          })
        }
      $scope.detail_ra=''
      //tampil detail ra
        $scope.$watch('ra_pilih', function(){
          $("#loading").modal('show');
          ra.Detail($scope.ra_pilih.id).then(function(response){
            $("#loading").modal('hide');
            $scope.detail_ra= response
            $scope.detail_ra.jum_masuk =0
            $scope.detail_ra.jum_keluar=0
            for(var i = 0 ; i<response.jenis_trans_masuk.length;i++){
              $scope.detail_ra.jum_masuk += response.jenis_trans_masuk[i].jml*1
            }
            for(var j = 0 ; j<response.jenis_trans_keluar.length;j++){
              $scope.detail_ra.jum_keluar += response.jenis_trans_keluar[j].jml*1
            }
            $scope.cek_aktif= function(){
              if(response.status=='0'){
                return true
              }
              else{
                return false
              }
            }
          })


        })

      $scope.tetapkan = function(){
        $("#loading").modal('show');
        ra.Tetapkan($scope.ra_pilih.id).then(function(response){
         $("#loading").modal('hide');
          $rootScope.addalert('success','Rencana Anggaran Ditetapkan');

          $scope.cek_aktif= function(){
            return false
          }
        })
      }

      $scope.print = function(){
        var judul= document.getElementById('judul').innerHTML;
        var tabel= document.getElementById('tabel_ra').innerHTML;
        helper.print(judul + tabel);
      }

      //tampil akun

      akun.Get().then(function(response){
        $scope.data_akun= response.data
      })



      $scope.hapus_jenis_anggaran = function(){
        if($scope.idhapus=='-'){

        }else{
        ra.Delete({id:$scope.ra_pilih.id,id_jenis:$scope.idhapus}).then(function(response){

            $rootScope.addalert('success','Data terhapus');

          if($scope.jenishapus=='m'){
            $scope.detail_ra.jenis_trans_masuk.splice($scope.indexhapus,1);
            $scope.detail_ra.jum_masuk -= $scope.ra_trans.jml
          }else{
            $scope.detail_ra.jenis_trans_keluar.splice($scope.indexhapus,1);
            $scope.detail_ra.jum_keluar -= $scope.ra_trans.jml
          }
        })
      }

    }

}])







//==============================================================================================
//==============__________======================================================================
//=============____====____=====================================================================
//============____======____====================================================================
//===========________________===================================================================
//===========_____=======____===================================================================
//===========_____=======____===================================================================
//==============================================================================================

app.controller('transaksi',['$scope','$rootScope','ra','$routeParams','akun','userdata','tapelService','jurnal',
function($scope,$rootScope,ra,$routeParams,akun,userdata,tapelService,jurnal){
    $scope.jenis_input_trans=$routeParams.jenis

    var tampil= function(){
      $("#loading").modal('show');
      jurnal.Get_per_page({
      akun:'all',
      mulai:'2000-08-08',
      akhir:$rootScope.Dt.y+'-'+$rootScope.Dt.m+'-'+$rootScope.Dt.d,
      index:'0',
      jumlah_tampil:'10'
    }).then(function(response){
       $("#loading").modal('hide');
      $scope.daftar_transaksi=response.data
    })
  }
  tampil();

    $scope.formtrans={}
    $scope.no=0;
    ra.Cari_tahun({
      tapel:tapelService.tapel_2th[1],
      jenis:'m'
    }).then(function(response){
      $scope.daftar_trans_masuk=response;
    })
    ra.Cari_tahun({
      tapel:tapelService.tapel_2th[1],
      jenis:'k'
    }).then(function(response){
      $scope.daftar_trans_keluar=response;
    })
      $scope.jenis_trans_pilih={}
      $scope.pilih = function(sub){
      $scope.jenis_trans_pilih=sub
      $scope.formtrans.uraian=sub.nm_jenis_trans
      var extra= JSON.parse(sub.extra)
      $scope.formtrans.debet=extra.debet
      $scope.formtrans.kredit=extra.kredit

    }


    akun.Get().then(function(response){
      $scope.data_akun= response.data
    })
    $scope.add_transaksi= function(){
       $("#loading").modal('show');
      var data_trans={
        id_kontak:$scope.formtrans.kontak,
        uraian:$scope.formtrans.uraian,
        jumlah:$scope.formtrans.nominal,
        debet:$scope.formtrans.debet,
        kredit:$scope.formtrans.kredit,
        id_jenis:$scope.jenis_trans_pilih.id
      }
      jurnal.Add(data_trans).then(function(response){
        tampil();
        jurnal.Get_per_page({
        akun:'all',
        mulai:'2000-08-08',
        akhir:$rootScope.Dt.y+'-'+$rootScope.Dt.m+'-'+$rootScope.Dt.d,
        index:'0',
        jumlah_tampil:'10'
      }).then(function(response){
        $scope.daftar_transaksi=response.data
      })
      });
      $rootScope.addalert('success','transaksi tercatat');
      $scope.formtrans={}

    }



}])








//=========================================================================================
//=========================================================================================
//=========================================================================================

app.controller('akun',['$scope','akun','$rootScope',
    function($scope,akun,$rootScope){
    $("#loading").modal('show');
    akun.Get().then(function(response){
       $("#loading").modal('hide');
        $scope.data_akun = response.data
    })
    $scope.jenis_akun_terpilih=''
    $scope.jenis_akun = akun.Jenis
    $scope.ubah = function(){
       $("#loading").modal('show');
      akun.Update($scope.form_akun).then(function(response){
         $("#loading").modal('hide');
        $rootScope.addalert('success','Akun Diubah');
        $scope.form_akun={}
      })
    };
    $scope.add = function(){
       $("#loading").modal('show');
      akun.Add($scope.form_akun).then(function(response){
         $("#loading").modal('hide');
        $rootScope.addalert('success','Akun Ditambahkan');
        $scope.data_akun.push($scope.form_akun);
        $scope.form_akun={}
      })
    }
    $scope.delete = function(){
       $("#loading").modal('show');
      akun.Delete($scope.form_akun).then(function(response){
         $("#loading").modal('hide');
        $rootScope.addalert('success','Akun Dihapus');
        $scope.data_akun.splice($scope.position,1);
        $scope.form_akun={}
      })
    }
}])










//===========================================================
//===========================================================
//===========================================================
//===========================================================












//===========================================================
//===========================================================
//===========================================================

app.controller('profil',['$scope','User','$rootScope',
function($scope,User,$rootScope){
  User.Detail({id:$rootScope.userdata.user_id}).then(function(response){
    $scope.form_userdata = response
  })

  $scope.aksi='edit'
  $scope.save= function(){
      User.Update({user_id:$scope.form_userdata.user_id,
      display_name:$scope.form_userdata.display_name,
      address:$scope.form_userdata.address,
      phone:$scope.form_userdata.phone,
      bio:$scope.form_userdata.bio}).then(function(response){
        $rootScope.addalert('success','Data Diubah');
        console.log(response);
      })
  }
}])

app.controller('showprofil',['$scope','User','$rootScope','$routeParams',
function($scope,User,$rootScope,$routeParams){
 $scope.aksi='show';
 $scope.$on('$routeChangeSuccess', function() {
    // $routeParams should be populated here
     $scope.user_id = $routeParams.id;
     User.Detail({id:$scope.user_id}).then(function(response){
         $scope.profil =  response
     });
  });



}])


app.controller('jurnal',['$scope','jurnal','helper','tapelService',
function($scope,jurnal,helper,tapelService){
  var TodayDate = new Date();


  $scope.daftar_bulan=helper.daftar_bulan

  $scope.bulan_pilihan=$scope.daftar_bulan[TodayDate.getMonth()]
  var jumlah_tampil=5

  var tampil_jumlah_data= function(config){
     $("#loading").modal('show');
    jurnal.Get({
      akun : 'all',
      mulai: config.mulai,
      akhir: config.selesai
    }).then(function(response){
       $("#loading").modal('hide');
      $scope.jurnal_asli=response.data
      $scope.jumlah_data=response.num_rows
      $scope.paging=[]
      for(var i=0; i<(response.num_rows/2/jumlah_tampil);i++){
        $scope.paging.push({'page':i,'index':(response.num_rows/2)*i})
      }
      config.index=0
      tampil_jurnal(config)
    })
  }
  var tampil_jurnal= function(config){
    jurnal.Get_per_page({
      akun : 'all',
      mulai: config.mulai,
      akhir: config.selesai,
      index: config.index,
      jumlah_tampil: jumlah_tampil*2
    }).then(function(response){
      $scope.daftar_jurnal=response.data
    })

  }

  $scope.print= function(){
    var judul= '<h2>' + document.getElementById('judul').innerHTML + '</h2>'
    var data= document.getElementById('print_table').innerHTML
    helper.print(judul + data);
  }

  $scope.tampil_halaman= function(halaman){

    //memanggil tampil jurnal



    tampil_jurnal({

      mulai:'2015-'+ $scope.bulan_pilihan.id +'-01',
      selesai:'2016-'+ $scope.bulan_pilihan.id+'-31',
      index:jumlah_tampil*2*halaman
    })
  }

  $scope.$watch('bulan_pilihan',function(){
    var tapel = tapelService.tapel_sekarang;
    console.log(tapel.substring(0, 2))
    console.log(tapel.substring(2, 2));
//memanggil jumlah halaman untuk tiap bulan
    tampil_jumlah_data({
      mulai:'2016-'+ $scope.bulan_pilihan.id +'-01',
      selesai:'2016-'+ $scope.bulan_pilihan.id+'-31'
    })


  })
}])








//=======================================================================
//=======================================================================
//=======================================================================
//=======================================================================


app.controller('buku_besar',['$scope','jurnal','helper','akun',
function($scope,jurnal,helper,akun){

  $scope.daftar_bulan=helper.daftar_bulan
  var TodayDate = new Date();
  $scope.pilihan=$scope.daftar_bulan[TodayDate.getMonth()]
  $scope.pilihan.akun='001'
  akun.Get().then(function(response){
    $scope.daftar_akun=response.data
    $scope.pilihan.akun=response.data[0].id_akun
    $scope.pilihan.nama_akun=response.data[0].nama_akun

  })

  var tampil= function(){
     $("#loading").modal('show');
    var bulan=$scope.pilihan.id
    jurnal.Get({
      akun : $scope.pilihan.akun,
      mulai:'2015-07-01',
      akhir:'2016-06-31'
    }).then(function(response){
     $("#loading").modal('hide');
      $scope.daftar_jurnal=response.data
      $scope.saldo={}
      $scope.saldo.jum_debet=0
      $scope.saldo.jum_kredit=0
      for(var i = 0;i<response.data.length;i++){
        $scope.saldo.jum_debet=$scope.saldo.jum_debet+parseInt(response.data[i].debet)
        $scope.saldo.jum_kredit=$scope.saldo.jum_kredit+parseInt(response.data[i].kredit)
      }
  var saldo=$scope.saldo.jum_kredit-$scope.saldo.jum_debet
      if(saldo<0){
        $scope.saldo.jenis='debet'
      }
      else{
        $scope.saldo.jenis='kredit'
      }
      $scope.saldo.total=Math.abs(saldo)


    })
  }
  tampil()
  $scope.tampil= function(){
    tampil();
  }


}])




//=======================================================================
//=======================================================================
//=======================================================================
//=======================================================================
app.controller('neraca',['$scope','Saldo','helper','$rootScope',
function($scope,Saldo,helper,$rootScope){
  $scope.tanggal= (new Date).toLocaleFormat("%A, %e %B, %Y");
  $rootScope.full =true;
    Saldo.Show().then(function(output){
      $scope.daftar_saldo=output

      $scope.saldo_aset = 0
      $scope.saldo_not_aset = 0
      for(var i = 0;i<output.length;i++){
        var debet=output[i].debet
        var kredit=output[i].kredit
        $scope.daftar_saldo[i].debet=debet-kredit
        $scope.daftar_saldo[i].kredit=kredit-debet
        if($scope.daftar_saldo[i].debet<0){
          $scope.daftar_saldo[i].debet=0
        }
        if($scope.daftar_saldo[i].kredit<0){
          $scope.daftar_saldo[i].kredit=0
        }
        if($scope.daftar_saldo[i].jenis_akun =='a')
            $scope.saldo_aset = $scope.saldo_aset + ($scope.daftar_saldo[i].debet-$scope.daftar_saldo[i].kredit)
        else
          $scope.saldo_not_aset = $scope.saldo_not_aset + ($scope.daftar_saldo[i].kredit-$scope.daftar_saldo[i].debet)


      }


    })
    $scope.not_aset=[]
    for(var i=1;i<5;i++){
      $scope.not_aset.push($rootScope.Jenis_akun[i]);
    }
    $scope.print= function(){
      var output = document.getElementById("neraca").innerHTML;
      helper.print(output)
    }
}])


//============================================================================
//============================================================================
//============================================================================
//============================================================================

app.controller('neraca_lajur',['$scope','Saldo','helper','$rootScope','tapelService','ra',
function($scope,Saldo,helper,$rootScope,tapelService,ra){


    $scope.print= function(){
      var output = document.getElementById("datalajur").innerHTML;
      helper.print(output)
    }
    $scope.pilihan_tahun=tapelService.tapel_sekarang
    $scope.daftar_ta=[]
    ra.Get().then(function(response){
      for(var i = 0; i<response.length; i++){
        $scope.daftar_ta.push({"id":response[i].tahun_anggaran,
        "nama":"Tahun 20"+response[i].tahun_anggaran.substr(0,2)+"/20"+response[i].tahun_anggaran.substr(2,2)})
      }
    })
    $scope.$watch('pilihan_tahun',function(){

      $scope.jumlah={
        saldo:{
          debet:0,
          kredit:0},
        penyesuaian:{
          debet:0,
          kredit:0},
        rl:{
          debet:0,
          kredit:0},
        neraca:{
          debet:0,
          kredit:0}
      };
      tampil($scope.pilihan_tahun)
    })

    var tampil = function(tahun){
 $("#loading").modal('show');
        Saldo.Neraca_lajur(tahun).then(function(response){
           $("#loading").modal('hide');
        $scope.data_neraca_lajur = response;
        for(var i = 0 ; i < response.length; i++){
          $scope.jumlah.saldo.debet += $scope.data_neraca_lajur[i].saldo.debet*1;
          $scope.jumlah.saldo.kredit += $scope.data_neraca_lajur[i].saldo.kredit*1;
          $scope.jumlah.penyesuaian.debet += $scope.data_neraca_lajur[i].penyesuaian.debet*1;
          $scope.jumlah.penyesuaian.kredit += $scope.data_neraca_lajur[i].penyesuaian.kredit*1;
          $scope.jumlah.rl.debet += $scope.data_neraca_lajur[i].rl.debet*1;
          $scope.jumlah.rl.kredit += $scope.data_neraca_lajur[i].rl.kredit*1;
          $scope.jumlah.neraca.debet += $scope.data_neraca_lajur[i].neraca.debet*1;
          $scope.jumlah.neraca.kredit += $scope.data_neraca_lajur[i].neraca.kredit*1;
        }
        $scope.rl={}
        $scope.rlneraca={}
        if($scope.jumlah.rl.debet-$scope.jumlah.rl.kredit > 0){
            $scope.rl.debet = $scope.jumlah.rl.debet-$scope.jumlah.rl.kredit;
        }else{
          $scope.rl.debet = 0;
        }
        if($scope.jumlah.rl.kredit-$scope.jumlah.rl.debet > 0){
        $scope.rl.kredit = $scope.jumlah.rl.kredit-$scope.jumlah.rl.debet;
        }
        else{
          $scope.rl.kredit =0;
        }

        if($scope.jumlah.neraca.debet-$scope.jumlah.neraca.kredit > 0){
            $scope.rlneraca.debet = $scope.jumlah.neraca.debet-$scope.jumlah.neraca.kredit;
        }else{
          $scope.rlneraca.debet = 0;
        }
        if($scope.jumlah.neraca.kredit-$scope.jumlah.neraca.debet > 0){
        $scope.rlneraca.kredit = $scope.jumlah.neraca.kredit-$scope.jumlah.neraca.debet;
        }
        else{
          $scope.rlneraca.kredit =0;
        }
      })
    }



}])

//============================================================================
//============================================================================
//============================================================================
//============================================================================

app.controller('user',['$scope','userdata','$rootScope',
    function($scope,userdata,$rootScope){

      $scope.pilihan={}
        $scope.pilihan.lstpriv = ["1","1","1","0"]

        function tampil(){
           $("#loading").modal('show');
          userdata.AllUser($rootScope.userdata).then(function(response){
            $("#loading").modal('hide');
            $scope.user=response.data
            $scope.form_user={}
            $scope.form_user.user_group=response.data[0].user_group;
          })
        }
        tampil()
        $scope.add_user = function(){
          var privilege=''
          for(var i=0;i<$scope.pilihan.lstpriv.length;i++){

              privilege +=$scope.pilihan.lstpriv[i]

          }
          $scope.form_user.privilege= privilege;
          $scope.form_user.user_level = '2';
          $scope.form_user.password = 'hahahaha';
          userdata.Daftar($scope.form_user).then(function(response){
            	 $("#loading").modal('hide');
               $rootScope.addalert('success','user ditambahkan')
               tampil();
          })
        }
        $scope.pilih= function(data){
          $scope.pilihan=data
          var lstpriv=data.privilege.split("");
          $scope.pilihan.lstpriv=[];
          for(var i=0; i<lstpriv.length; i++){
              $scope.pilihan.lstpriv[i]=lstpriv[i]

          }
        }
        $scope.edit_priv = function(){
           $("#loading").modal('show');
          var data={}
          var privilege=''
          data.user_id=$scope.pilihan.user_id
          for(var i=0;i<$scope.pilihan.lstpriv.length;i++){

              privilege +=$scope.pilihan.lstpriv[i]

          }

          data.privilege=privilege
          console.log(data);
          userdata.Update(data).then(function(response){
             $("#loading").modal('hide');
            $rootScope.addalert('success','Hak Akses Diubah')
            tampil()
          })
        }
    }])


//============================================================================
//============================================================================
//============================================================================
//============================================================================

app.controller('penyesuaian',['$scope','$rootScope','akun','jurnal',
function($rootScope,$scope,akun,jurnal){
 $("#loading").modal('show');
  akun.Saldo().then(function(response){
     $("#loading").modal('hide');
    $scope.data_akun= response
  })



  $scope.frm_jurnal={}
  $scope.add_transaksi= function(){
     $("#loading").modal('show');
    jurnal.Add($scope.frm_jurnal)
     $("#loading").modal('hide');
    $rootScope.addalert('success','Transaksi tersimpan');

      $scope.frm_jurnal={}
  }


}])


//============================================================================
//============================================================================
//============================================================================
//============================================================================

app.controller('setting',['$scope','$rootScope','userdata',
function($scope,$rootScope,userdata){
//  localStorage.setItem('theme')
  $scope.theme=localStorage.getItem('theme');
  $scope.pilih_tema = function(){
    localStorage.setItem('theme',$scope.theme);
    $rootScope.theme=$scope.theme;
  }
  $scope.lsttheme = ['brave','calm','darkly','metro','simplex','sweet','unity','yeti'];

  $scope.cek_password= function(){
    userdata.Check_pwd({'email':$rootScope.userdata.user_email,'password':$scope.resetpwd.passwordlama}).then(
      function(res){
        console.log(res);
        if(res.auth=='0'){
          $scope.password_status='salah';
        }
        else{
            $scope.password_status='benar';

        }
      })
  }
  $scope.ubah_password= function(){

    userdata.Update({
      'user_id':$rootScope.userdata.user_id,
      'password':$scope.resetpwd.password
    }).then(function(res){
      if(res.message=='ok'){
        $rootScope.addalert('success','Password diubah');
        $scope.resetpwd={}
      }
    })
  }
  $scope.ubah_email = function(){
    userdata.Update({
      'user_id':$rootScope.userdata.user_id,
      'email':$scope.chemail
    }).then(function(res){
      if(res.message=='ok'){
        $("#wait").modal('hide');
        $("#ubahemail").modal('show');
        $scope.chemail="";
      }
    })
  }
  $scope.nonaktifkan = function(){
    userdata.Update({
      'user_id':$rootScope.userdata.user_id,
      'status':'2'
    }).then(function(res){
      if(res.message=='ok'){
        $rootScope.addalert('success','Akun dinonaktifkan');
        window.location = '.'
      }
    })
  }
}])



app.controller('penggunaan-dana',['$scope','$rootScope','userdata','ra','tapelService','helper',
function($scope,$rootScope,userdata,ra,tapelService,helper){

  $scope.print= function(){
    var output = document.getElementById("data").innerHTML;
    helper.print(output)
  }

  var tampil_ra= function(){

    ra.Get().then(function(response){

      $scope.ra=response;
    })
  }

  tampil_ra();
    ra.Tahun({'tahun':tapelService.tapel_sekarang}).then(function(response){
      $scope.ra_pilih = response[0];

    })
    $scope.$watch('ra_pilih', function(){
      $("#loading").modal('show');
      console.log($scope.ra_pilih);
      ra.Detail($scope.ra_pilih.id).then(function(response){

        $scope.detail_ra= response
        $scope.detail_ra.jum_masuk =0
        $scope.detail_ra.jum_keluar=0
        $scope.detail_ra.jum_real_masuk=0
        $scope.detail_ra.jum_real_keluar=0
        for(var i = 0 ; i<response.jenis_trans_masuk.length;i++){
          $scope.detail_ra.jum_masuk += response.jenis_trans_masuk[i].jml*1
          $scope.detail_ra.jum_real_masuk += response.jenis_trans_masuk[i].realisasi*1
        }
        for(var j = 0 ; j<response.jenis_trans_keluar.length;j++){
          $scope.detail_ra.jum_keluar += response.jenis_trans_keluar[j].jml*1
          $scope.detail_ra.jum_real_keluar += response.jenis_trans_keluar[j].realisasi*1
        }
        console.log('response'+response.status)
        $scope.cek_aktif= function(){
          if(response.status=='0'){
            return true

          }
          else{
            return false
          }
        }

      })
      ra.Realisasi($scope.ra_pilih.id).then(function(response){
        $("#loading").modal('hide');
        $scope.realisasi_ra= response
        $scope.realisasi_ra.jum_masuk =0
        $scope.realisasi_ra.jum_keluar=0
        $scope.realisasi_ra.jum_real_masuk=0
        $scope.realisasi_ra.jum_real_keluar=0
        for(var i = 0 ; i<response.jenis_trans_masuk.length;i++){
          $scope.realisasi_ra.jum_masuk += response.jenis_trans_masuk[i].jml*1
          $scope.realisasi_ra.jum_real_masuk += response.jenis_trans_masuk[i].realisasi*1
        }
        for(var j = 0 ; j<response.jenis_trans_keluar.length;j++){
          $scope.realisasi_ra.jum_keluar += response.jenis_trans_keluar[j].jml*1
          $scope.realisasi_ra.jum_real_keluar += response.jenis_trans_keluar[j].realisasi*1
        }
        $scope.cek_aktif= function(){
          if(response.status=='0'){
            return true
          }
          else{
            return false
          }

        }
      })
    })
}])
