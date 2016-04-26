

app.run(['$rootScope','Saldo','userdata','people','User_group','akun',
function($rootScope,Saldo,userdata,people,User_group,akun){
userdata.cekAuth()
console.log(userdata.token);

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
    $rootScope.tampil_saldo()

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
  people.Detail({'id':response.user_id}).then(function(res){
    $rootScope.userdata.nama = res.data[0].nama
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


app.controller('home',['$scope','$routeParams','$rootScope','Saldo',
function($scope,$routeParams,$rootScope,Saldo){

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
      })
    }
    tampil_ra();

//tambahkan rencana anggaran
    $scope.add_ra= function(){
      var data = {
        nm_anggaran: $scope.nm_anggaran,
        tahun_anggaran: $scope.lsttapel[2],
        pencatat:$rootScope.userdata.user_id
      }
      ra.Add(data).then(function(response){
        $scope.ra.push(data)
        $rootScope.addalert('success','Rencana Anggaran dibuat');
        data.id= response.id.substring(0, 5);
        $scope.ra_pilih=data;
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
                $scope.detail_ra.jenis_trans_keluar.push(
                  {
                    nm_jenis_trans: $scope.ra_trans.nm_jenis_trans,
                    sumber_dana: $scope.ra_trans.sumber_dana,
                    debet: $scope.ra_trans.debet,
                    kredit: $scope.ra_trans.kredit,
                    jenis_trans: mk,
                    sub: $scope.subjenis
                  }
                )
            }
            else{
                $scope.detail_ra.jenis_trans_masuk.push({
                  nm_jenis_trans: $scope.ra_trans.nm_jenis_trans,
                  sumber_dana: $scope.ra_trans.sumber_dana,
                  debet: $scope.ra_trans.debet,
                  kredit: $scope.ra_trans.kredit,
                  jenis_trans: mk,
                  sub: $scope.subjenis
                })
            }
            $scope.ra_trans={}
            $scope.subjenis=[{}]
            $rootScope.addalert('success','Jenis transaksi ditambahkan');

          })
        }
      $scope.detail_ra=''
      //tampil detail ra
        $scope.$watch('ra_pilih', function(){
          ra.Detail($scope.ra_pilih.id).then(function(response){
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
              if(response.tahun_anggaran > $scope.tapel_sekarang && response.status=='0'){
                return true
              }
              else{
                return false
              }
            }
          })


        })

      $scope.tetapkan = function(){
        ra.Tetapkan($scope.ra_pilih.id).then(function(response){
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
}])







//==============================================================================================
//==============__________======================================================================
//=============____====____=====================================================================
//============____======____====================================================================
//===========________________===================================================================
//===========_____=======____===================================================================
//===========_____=======____===================================================================
//==============================================================================================

app.controller('transaksi',['$scope','$rootScope','ra','$routeParams','akun','userdata','tapelService','people','jurnal',
function($scope,$rootScope,ra,$routeParams,akun,userdata,tapelService,people,jurnal){
    $scope.jenis_input_trans=$routeParams.jenis

    jurnal.Get_per_page({
      akun:'all',
      mulai:'2000-08-08',
      akhir:$rootScope.Dt.y+'-'+$rootScope.Dt.m+'-'+$rootScope.Dt.d,
      index:'0',
      jumlah_tampil:'10'
    }).then(function(response){
      $scope.daftar_transaksi=response.data
    })

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

    people.Get().then(function(response){
      $scope.people=response.data;
    })

    akun.Get().then(function(response){
      $scope.data_akun= response.data
    })
    $scope.add_transaksi= function(){
      var data_trans={
        id_kontak:$scope.formtrans.kontak,
        uraian:$scope.formtrans.uraian,
        jumlah:$scope.formtrans.nominal,
        debet:$scope.formtrans.debet,
        kredit:$scope.formtrans.kredit,
        id_jenis:$scope.jenis_trans_pilih.id
      }
      jurnal.Add(data_trans);
      $rootScope.addalert('success','transaksi tercatat');
      $rootScope.tampil_saldo()
      $scope.formtrans={}
    }



}])








//=========================================================================================
//=========================================================================================
//=========================================================================================

app.controller('akun',['$scope','akun','$rootScope',
    function($scope,akun,$rootScope){

    akun.Get().then(function(response){
        $scope.data_akun = response.data
    })
    $scope.jenis_akun_terpilih=''
    $scope.jenis_akun = akun.Jenis
    $scope.ubah = function(){
      akun.Update($scope.form_akun).then(function(response){
        $rootScope.addalert('success','Akun Diubah');
        $scope.form_akun={}
      })
    };
    $scope.add = function(){
      akun.Add($scope.form_akun).then(function(response){
        $rootScope.addalert('success','Akun Ditambahkan');
        $scope.data_akun.push($scope.form_akun);
        $scope.form_akun={}
      })
    }
    $scope.delete = function(){
      akun.Delete($scope.form_akun).then(function(response){
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

app.controller('people',['$scope','people','$rootScope',
function($scope,people,$rootScope){

    people.Get().then(function(response){
        $scope.people = response.data;
    })

    $scope.add = function(){
      people.Add($scope.detail).then(function(response){
        $rootScope.addalert('success','Data Ditambahkan');
        $scope.people.push($scope.detail)
        $scope.detail={}
      })

    }

}])










//===========================================================
//===========================================================
//===========================================================

app.controller('profil',['$scope','people','$rootScope',
function($scope,people,$rootScope){
  $scope.form_userdata= $rootScope.userdata;
  $scope.form_userdata.id = $rootScope.userdata.user_id;

  $scope.save= function(){
    people.Detail({'id':$scope.form_userdata.user_id}).then(function(res){
      if(res.num_rows>0){
      }
      else{
        people.Add($scope.form_userdata).then(function(response){
          $rootScope.addalert('success','Profil disimpan');

        })
      }

    })
  }
}])

app.controller('jurnal',['$scope','jurnal','helper',
function($scope,jurnal,helper){
  var TodayDate = new Date();


  $scope.daftar_bulan=helper.daftar_bulan

  $scope.bulan_pilihan=$scope.daftar_bulan[TodayDate.getMonth()]
  var jumlah_tampil=5

  var tampil_jumlah_data= function(config){
    jurnal.Get({
      akun : 'all',
      mulai: config.mulai,
      akhir: config.selesai
    }).then(function(response){
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
    tampil_jurnal({
      mulai:'2015-'+ $scope.bulan_pilihan.id +'-01',
      selesai:'2016-'+ $scope.bulan_pilihan.id+'-31',
      index:jumlah_tampil*2*halaman
    })
  }

  $scope.$watch('bulan_pilihan',function(){

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
    var bulan=$scope.pilihan.id
    jurnal.Get({
      akun : $scope.pilihan.akun,
      mulai:'2015-07-01',
      akhir:'2016-06-31'
    }).then(function(response){

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

app.controller('user',['$scope','userdata','$rootScope',
    function($scope,userdata,$rootScope){

        function tampil(){
          userdata.AllUser($rootScope.userdata).then(function(response){
            $scope.user=response.data
            $scope.form_user={}
            $scope.form_user.user_group=response.data[0].user_group;
          })
        }
        tampil()
        $scope.add_user = function(){
          userdata.Daftar($scope.form_user).then(function(response){
          })
        }
        $scope.pilih= function(data){
          $scope.pilihan=data
          var lstpriv=data.privilege.split("");
          $scope.pilihan.lstpriv=[];
          for(var i=0; i<lstpriv.length; i++){
            if(lstpriv[i]=='1'){
              $scope.pilihan.lstpriv[i]=true
            }
            else {
              $scope.pilihan.lstpriv[i]=false
            }
          }
        }
        $scope.edit_priv = function(){
          var data={}
          var privilege=''
          data.user_id=$scope.pilihan.user_id
          for(var i=0;i<$scope.pilihan.lstpriv.length;i++){
            if($scope.pilihan.lstpriv[i]==true){
              privilege +='1'+''
            }
            else{
              privilege +='0'+''
            }
          }
          data.priv={}
          data.priv.privilege=privilege
          userdata.Update(data).then(function(response){
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

  akun.Get().then(function(response){
    $scope.data_akun= response.data
  })
  $scope.frm_jurnal={}
  $scope.add_transaksi= function(){
    console.log($scope.frm_jurnal.jenis_kredit);
    if($scope.frm_jurnal.jenis_kredit=='a' || $scope.frm_jurnal.jenis_kredit=='b'){
      if($scope.frm_jurnal.saldo_kredit < $scope.frm_jurnal.jumlah ){
          $rootScope.addalert('danger','Saldo tidak cukup!');
        }
      else {
        jurnal.Add($scope.frm_jurnal)
        $rootScope.addalert('success','Transaksi tersimpan');
      }
      console.log('haha');
    }
    else if($scope.frm_jurnal.jenis_debet=='m' || $scope.frm_jurnal.jenis_debet=='p' || $scope.frm_jurnal.jenis_debet=='k'){
      if($scope.frm_jurnal.saldo_debet < $scope.frm_jurnal.jumlah ){
          $rootScope.addalert('danger','Saldo tidak cukup!');
        }
      else {
        jurnal.Add($scope.frm_jurnal)
        $rootScope.addalert('success','Transaksi tersimpan');
      }
    }
  }


}])
