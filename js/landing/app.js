var app= angular.module('landing-page',['ngRoute']);


app.config(['$routeProvider','$locationProvider',function($routeProvider,$locationProvider){
		$routeProvider

		    // route for the home page
      .when('/', {
                templateUrl : 'partial/landing/home.html',
                controller  : 'home'
            })
      .when('/daftar',{
              templateUrl : 'partial/landing/register.html',
              controller  : 'daftar'
      })
			.when('/daftar/verifikasi-email',{
              templateUrl : 'partial/landing/register-step2.html',
              controller  : 'verifikasi'
      })
			.when('/daftar/last-step',{
              templateUrl : 'partial/landing/tunggu.html',
              controller  : 'tunggu'
      })



	}]);


	app.run(['$rootScope',function($rootScope){


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




  app.controller('home',['$scope','$http','User','JWT','$rootScope',
      function($scope,$http,User,JWT,$rootScope){
					$scope.login = function(){
						var data = JSON.stringify({
							email: $scope.username,
							password: $scope.password
					 })
					 User.Login(data).then(function(response){
							if(response.auth==1){
									localStorage.setItem('token', response.token);
									var userdata = JWT.decode(localStorage.getItem('token'));
									console.log(userdata.user_level);
									if(userdata.user_level=='1'){
										window.location = "admin-dashboard.html";
									}
									else if (userdata.user_level=='2') {
										window.location = "dashboard.html";
									}
							}
							else{
									console.log('login error');
									$rootScope.addalert('danger',response.msg)
							}
		       })
					}

      }]);
  app.controller('daftar',['$scope','$http','$location','User',
  function($scope,$http,$location,User){
$scope.form_register={}

					$scope.daftar = function(){
						var data = JSON.stringify($scope.form_register)
						User.Daftar(data).then(function(response){
							console.log(response);
            	 $("#ModalDaftar").modal('hide');
             	 $("#ModalSuccess").modal('show');

						})
					}

					$scope.cek_email= function(){
						var data = JSON.stringify($scope.form_register)
						User.Cek_email(data).then(function(response){
							console.log(response);
							if(response.status=="error"){
								$scope.email_status='has-error'
							}
							else{
								$scope.email_status=''
							}
						})
					}
					$scope.cek_nip = function(){
						User.Cek_nip($scope.form_register).then(function(response){
							console.log(response.num_rows);
							if(response.num_rows>0){
								$scope.nip_status='has-error'
							}
							else{
								$scope.nip_status=''
							}

						})
					}



					$scope.next_step =function(){

						$("#ModalSuccess").modal('hide');
						$scope.daftar_klik=true

					}

      }]);
			app.controller('verifikasi',['$scope','User','$location',function($scope,User,$location){
					$scope.verifikasi= function(){
						User.Verifikasi($scope.form_verif).then(function(response){
							$location.path("daftar/last-step");
						})
					}
			}]);
			app.controller('tunggu',['$scope',function($scope){

			}]);

			app.factory("User", ['$http', function($http) {
			   return {
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
					 Login: function($params){
						 return $http({
							method:	'POST',
							url:'api/index.php/_session',
							header:{'Content-Type':'application/json'},
							data: $params
						}).then(function(response){
							return response.data;

						})
					},
					Verifikasi: function($params){
						return $http({
							method:	'GET',
							url:'api/index.php/user_verif/' + $params.token,
							header:{'Content-Type':'application/json'}
						}).then(function(response){
							return response.data;
						})
					},
					Cek_email: function($params){
						return $http({
							method:	'POST',
							url:'api/index.php/cek-email',
							data: $params,
							header:{'Content-Type':'application/json'}
						}).then(function(response){
							return response.data;
						})
					},
					Cek_nip: function($params){
						return $http({
							method:	'GET',
							url:'api/index.php/user/user_id='+$params.user_id+'/filter',
							header:{'Content-Type':'application/json'}
						}).then(function(response){
							return response.data;
						})
					}
			   }


			   }]);

//JWT
				 app.factory('JWT',function(){
	 			  return{
	 			    decode: function(token){
	 			      var base64Url = token.split('.')[1];
	 			      var base64 = base64Url.replace('-', '+').replace('_', '/');
	 			      return JSON.parse(window.atob(base64));
	 			    }

	 			  }
	 			})
