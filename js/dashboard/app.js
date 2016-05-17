var app= angular.module('dashboard',['ngRoute','ui.bootstrap']);


app.config(['$routeProvider','$locationProvider',function($routeProvider,$locationProvider){
		$routeProvider

		    // route for the home page
      .when('/', {
                templateUrl : 'partial/dashboard/home.html',
                controller  : 'home'
      })
      .when('/rencana-anggaran',{
                templateUrl : 'partial/dashboard/rencana_anggaran.html',
                controller  : 'rencana-anggaran'
      })
			.when('/transaksi',{
								templateUrl : 'partial/dashboard/transaksi.html',
								controller  : 'transaksi'

			})
			.when('/kelola-akun',{
								templateUrl : 'partial/dashboard/akun.html',
								controller  : 'akun'
			})
			.when('/kelola-daftar-orang',{
								templateUrl : 'partial/dashboard/people.html',
								controller  : 'people'
			})
			.when('/profil',{
								templateUrl : 'partial/dashboard/profil.html',
								controller  : 'profil'
			})
			.when('/jurnal',{
								templateUrl : 'partial/dashboard/jurnal.html',
								controller  : 'jurnal'
			})
			.when('/buku-besar',{
								templateUrl : 'partial/dashboard/buku_besar.html',
								controller  : 'buku_besar'
			})
			.when('/neraca',{
								templateUrl : 'partial/dashboard/neraca.html',
								controller  : 'neraca'
			})
			.when('/penggunaan-dana',{
								templateUrl : 'partial/dashboard/laporan_penggunaan_anggaran.html',
								controller  : 'penggunaan-dana'
			})
			.when('/lst_user',{
								templateUrl : 'partial/dashboard/user.html',
								controller  : 'user'
			})

			.when('/jurnal_penyesuaian',{
								templateUrl	:	'partial/dashboard/penyesuaian.html',
								controller	:	'penyesuaian'
			})

			.when('/neraca_lajur',{
								templateUrl	:	'partial/dashboard/neraca_lajur.html',
								controller	:	'neraca_lajur'
			})

			.when('/setting',{
								templateUrl	:	'partial/dashboard/setting.html',
								controller	:	'setting'
			})


	}]);

//====================================================================//
//_________________________________LOG________________________________/
//====================================================================//
app.factory("Log",['$http','helper',function($http,helper){
	return {
		Show: function(page){
			return $http({
				headers: {
				 'token':  localStorage.getItem('token'),
				 'Content-Type':'application/json'
				 },
				method:	'GET',
				url:'api/index.php/log/'+ page
			}).then(function(response){

					 return response.data

			})
		}
	}


}]);





//====================================================================//
//_____________________SALDO_-_FACTORY__________________________________
//====================================================================

app.factory("Saldo", ['$http','helper', function($http,helper) {
   return {
     Show: function(){
       return $http({
         headers: {
          'token':  localStorage.getItem('token'),
					'Content-Type':'application/json'
          },
         method:	'GET',
         url:'api/index.php/buku_besar/saldo_per_jenis/a'
       }).then(function(response){
				 if(response.data.error){
 						helper.go_home()
 					}
					else{
						return helper.set_output(response.data)
			 		}
       })
     },

		 Neraca_lajur: function(tapel){
       return $http({
         headers: {
          'token':  localStorage.getItem('token'),
					'Content-Type':'application/json'
          },
         method:	'GET',
         url:'api/index.php/neraca_lajur/'+tapel
       }).then(function(response){
				 if(response.data.error){
 						helper.go_home()
 					}
					else{
						return helper.set_output(response.data)
			 		}
       })
     }
   }


   }]);

	 app.factory("User_group", ['$http', function($http) {
	    return {
	      Detail: function($params){
	        return $http({
	          headers: {
	           'token':  localStorage.getItem('token'),
	 					'Content-Type':'application/json'
	           },
	          method:	'GET',
	          url:'api/index.php/sekolah/group_id='+ $params.user_group+'/filter'
	        }).then(function(response){
	          return response.data;
	        })
	      }
	    }


	    }]);


			app.factory("User", ['$http', function($http) {
			   return {
			     Detail: function($params){
			       return $http({
				         method:	'GET',
			         url:'api/index.php/user/'+$params.id,
			         header:{'Content-Type':'application/json'}
			       }).then(function(response){
			         return response.data;
			       })
					 },
						Update: function($params){
				       return $http({
					         method:	'PUT',
				         url:'api/index.php/user/'+$params.user_id,
				         header:{'Content-Type':'application/json'},
				         data: $params
				       }).then(function(response){
				         return response.data;
				       })
			     }
		    }
			}]);
   //======================================================================//
   //_____________________TAPEL_-_FACTORY__________________________________//
   //======================================================================//
   app.factory('tapelService', function() {
   var d = new Date();
     var tapel= function(tambah){
       var t1 = d.getFullYear()+tambah;
       var t2 = d.getFullYear()+1+tambah;
       var t3 = d.getFullYear()-1+tambah;

       var tapel='';
       if (d.getMonth()<6){
         var tapel=t3.toString().substr(2, 2)+t1.toString().substr(2, 2)
       }
       else{
         var tapel=t1.toString().substr(2, 2)+t2.toString().substr(2, 2)
       }
       return tapel;
     }
     var smt_ini=function(){
       var n = d.getMonth();
       var smt='';
       if (n<6){
         smt= 'genap';
       }
       else{
         smt= 'ganjil';
       }
       return smt;
     }
     return{
       tapel_2th: [tapel(-1),tapel(0),tapel(1)],
       tapel_sekarang:tapel(0),
       semester_ini:smt_ini
       }
   })

//================================================================
//______________________RAPBS_-_FACTORY_____________________________
//=================================================================
app.factory('ra',['$http','helper',function($http,helper){
  return {
    Add: function($params){
      return $http({
        headers: {
         token:  localStorage.getItem('token')
         },
				 data: JSON.stringify($params),
        method:	'POST',
        url:'api/index.php/rencana_anggaran'
      }).then(function(response){
				return helper.set_output(response.data)
      })
    },
		Get: function(){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/rencana_anggaran'
			}).then(function(response){
				if(response.data.error){
						helper.go_home(response.data.error)
				}
					return helper.set_output(response.data)
			})
		},
		Detail: function(id){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/rencana_anggaran/'+ id +'/detail/0'
			}).then(function(response){
				return helper.set_output(response.data)
			})
		},
		Realisasi: function(id){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/rencana_anggaran/'+ id +'/detail/1'
			}).then(function(response){
				return helper.set_output(response.data)
			})
		},
		Delete: function($params){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'DELETE',
				url:'api/index.php/rencana_anggaran/'+ $params.id +'/'+$params.id_jenis
			}).then(function(response){
				return helper.set_output(response.data)
			})
		},
		Tetapkan: function(id){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'PUT',
				data: JSON.stringify({
					status: '1'
				}),
				url:'api/index.php/rencana_anggaran/'+ id
			}).then(function(response){
				return helper.set_output(response.data)
			})
		},
		Cari_tahun: function(cond){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/rencana_anggaran/tapel='+ cond.tapel +'&jenis='+cond.jenis
			}).then(function(response){
				return helper.set_output(response.data)
			})
		},
		Tahun: function(cond){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/rencana_anggaran/tahun_anggaran='+ cond.tahun +'/find'
			}).then(function(response){
				return helper.set_output(response.data)
			})
		}
  }
}])


//====================================================================//
//_________________JENIS_TRANSAKSI_-_FACTORY___________________________
//====================================================================//

app.factory("jenis_transaksi", ['$http','helper', function($http,helper) {
   return {
     Add: function($params){
       return $http({
         headers: {
          token:  localStorage.getItem('token')
          },
				 data: JSON.stringify($params),
         method:	'POST',
         url:'api/index.php/rencana_anggaran/'+$params.ra
       }).then(function(response){
	 				return helper.set_output(response.data)
       })
     }
   }


}]);

//====================================================================//

app.factory("transaksi", ['$http','helper', function($http,helper) {
   return {

   }


}]);
//
//====================================================================//

app.factory('akun', ['$http','helper', function($http,helper) {
   return {
		 Jenis:  [{'id':'a','nama':'Aset'}
		 		,{'id':'m','nama':'Modal'}
				,{'id':'k','nama':'Kewajiban'}
			 	,{'id':'b','nama':'Beban'}
			 	,{'id':'p','nama':'Pendapatan'}
		 ],
  		Get: function(){
       return $http({
         headers: {
          token:  localStorage.getItem('token')
          },
				 method:	'GET',
         url:'api/index.php/akun'
       }).then(function(response){
 				return helper.set_output(response.data)
       })
     },
		 Saldo: function(){
       return $http({
         headers: {
          'token':  localStorage.getItem('token'),
					'Content-Type':'application/json'
          },
         method:	'GET',
         url:'api/index.php/saldo'
       }).then(function(response){
				 if(response.data.error){
 						helper.go_home()
 					}
					else{
						return helper.set_output(response.data)
			 		}
       })
     },
		 Update: function($params){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				data: JSON.stringify($params),
				method:	'PUT',
				url:'api/index.php/akun/'+$params.id
			}).then(function(response){
					return helper.set_output(response.data)
			})
		},
		Delete: function($params){
		 return $http({
			 headers: {
				token:  localStorage.getItem('token')
				},
			 method:	'DELETE',
			 url:'api/index.php/akun/'+$params.id_akun
		 }).then(function(response){
			 return helper.set_output(response.data)
		 	 })
	 },
		Add: function($params){
		 return $http({
			 headers: {
				token:  localStorage.getItem('token')
				},
			 data: JSON.stringify($params),
			 method:	'POST',
			 url:'api/index.php/akun'
		 }).then(function(response){

			 	return helper.set_output(response.data)
		 })
	 }
   }


}]);
//



//======================================================================//
//______________________JWT_-_FACTORY___________________________________//
//======================================================================//
app.factory('JWT',function(){
  return{
    decode: function(token){
      var base64Url = token.split('.')[1];
      var base64 = base64Url.replace('-', '+').replace('_', '/');
      return JSON.parse(window.atob(base64));
    }

  }
})


//=====================================================================//
//______________________USER_INFO_____________________________________
//=======================================================================

app.factory('userdata',['$http','helper', function($http,helper){
	var userlogin= function(){
		if(localStorage.getItem('token')==undefined){
			return false
		}
		else if(localStorage.getItem('token')=='undefined'){
			return false
		}
		else if(localStorage.getItem('token')==null){
			return false
		}
		else{
			return true
		}
  };
	var Token = localStorage.getItem('token');

	return {
		Check_pwd: function($params){
			return $http({
			 method:	'POST',
			 url:'api/index.php/_session',
			 header:{'Content-Type':'application/json'},
			 data: $params
		 }).then(function(response){
			 return response.data;

		 })
	 },
		 Get: function(){
			return $http({
				headers: {
				 token:  Token
				 },
				method:	'GET',
				url:'api/index.php/_session'
			}).then(function(response){
				if(response.data.error){
					helper.go_home()
				}
				else
				{
					return response.data;
				}

				})
		},
		Logout: function(){
				localStorage.removeItem('token');
				helper.go_home()
		},
		cekAuth: function(){
			if(userlogin()){

			}
			else {
				helper.go_home()
			}
		},
		token : Token
		,
		AllUser: function($params){
 			return $http({
 				headers: {
 				 token:  Token
 				 },
 				method:	'GET',
 				url:'api/index.php/user/user_group=' + $params.user_group + '/filter'
 			}).then(function(response){
					return response.data
			})
		},
		Daftar: function($params){
			return $http({
				method:	'POST',
				url:'api/index.php/user',
				header:{'Content-Type':'application/json'},
				data: $params
			}).then(function(response){
				return response.data;
			})
		},
		Update: function($params){
			return $http({
				method:	'PUT',
				url:'api/index.php/user/'+$params.user_id,
				header:{'Content-Type':'application/json'},
				data: JSON.stringify($params)
			}).then(function(response){
				return response.data;
			})
		}


	}
}])


//=====================================================================//
//________________________PEOPLE_____________________________________
//=======================================================================
app.factory('people',['$http','helper', function($http,helper){
	return {
		 Get: function($params){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/people'
			}).then(function(response){
					return helper.set_output(response.data)
			})
		},
		Add: function($params){
		 return $http({
			 headers: {
				token:  localStorage.getItem('token')
				},
				data: JSON.stringify($params),
			 method:	'POST',
			 url:'api/index.php/people'
		 }).then(function(response){

 				return helper.set_output(response.data)
		 })
	 },
	 	Detail: function($params){
			return $http({
				headers: {
			 		token:  localStorage.getItem('token')
			 	},
			 	method:	'GET',
				url:'api/index.php/people/id='+$params.id+'/filter'
				}).then(function(response){

				return helper.set_output(response.data)
				})
			}
	}
}])


//=====================================================================//
//________________________JURNAL_____________________________________
//=======================================================================
app.factory('jurnal',['$http','helper', function($http,helper){
	return {
		Add: function($params){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				data: JSON.stringify($params),
				method:	'POST',
				url:'api/index.php/transaksi/'+$params.id_jenis
			}).then(function(response){
				 return helper.set_output(response.data)
			})
		},
		 Get: function($params){
			return $http({
				headers: {
				 token:  localStorage.getItem('token')
				 },
				method:	'GET',
				url:'api/index.php/buku_besar/jurnal/'+ $params.akun +'/'+$params.mulai+'/'+ $params.akhir
			}).then(function(response){
				return helper.set_output(response.data)
			})
		},
		Get_per_page :  function($params){
		 return $http({
			 headers: {
				token:  localStorage.getItem('token')
				},
			 method:	'GET',
			 url:'api/index.php/buku_besar/jurnal/'+ $params.akun +'/'+$params.mulai+'/'+ $params.akhir +'/'+$params.index+'-'+$params.jumlah_tampil
		 }).then(function(response){
				return helper.set_output(response.data)
		 })
	 }
	}
}])

app.factory('helper',function(){
	return {
		daftar_bulan:[
	    {"id":"01","nama":"Januari"},
	    {"id":"02","nama":"Pebruari"},
	    {"id":"03","nama":"Maret"},
	    {"id":"04","nama":"April"},
	    {"id":"05","nama":"Mei"},
	    {"id":"06","nama":"Juni"},
	    {"id":"07","nama":"Juli"},
	    {"id":"08","nama":"Agustus"},
	    {"id":"09","nama":"September"},
	    {"id":"10","nama":"Oktober"},
	    {"id":"11","nama":"November"},
	    {"id":"12","nama":"Desember"}
	  ],
		go_home: function(msg){
			window.location='.'
		},
		set_output: function(data){
			if(data.error){
				alert(msg)
				setTimeout(function(){
					this.go_home(data.error)
				},1000)
			}
			else
			{
				localStorage.setItem('token', data.token);
				return data.response;
			}
		},
		print: function(element){
			localStorage.setItem('print',element);

			var OpenWindow = window.open("print.html", "mywin", '');
		}
	}
})
