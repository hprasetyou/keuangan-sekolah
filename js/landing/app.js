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

			.when('/verifikasi&token=:token',{
							templateUrl : 'partial/landing/verifikasi.html',
							controller  : 'verifikasi'
			})
			.when('/lost-password',{
							templateUrl : 'partial/landing/lost-password.html',
							controller  : 'lost-password'
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
							password: $scope.password,
							privilege:'1110'
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
            	 $("#loading").modal('hide');
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
						window.location='.'
					}

      }]);
			app.controller('verifikasi',['$scope','$rootScope','JWT','User','$location','$routeParams',
			function($scope,$rootScope,JWT,User,$location,$routeParams){

				$scope.token = $routeParams.token;
				$scope.$watch('token',function(){
						console.log($scope.token);
						User.Verifikasi({token:$scope.token}).then(function(response){
									console.log(response);
									$scope.verif= response;
								})
				})
			//			User.Verifikasi($routeParams.token).then(function(response){
				//			console.log(response);
					//		$scope.verif= response;
						//})
						$scope.ubahpassword = function(){
							var data = JWT.decode($scope.token)
							var datauser = {}
							datauser.user_id = data.user_id
							datauser.password = $scope.new_pwd
							console.log(datauser);
							User.Update(datauser).then(function(){
								$rootScope.addalert('success','password diset')
								window.location='.';
							})

						}

			}]);
			app.controller('lost-password',['$scope','User',
			function($scope,User){
					$scope.ubah = function(){
						console.log($scope.reset);
						User.Ch_pwd($scope.reset).then(function(response){
							$("#loading").modal('hide');
					 	 $("#ModalSuccess").modal('show');
						})

					}

					$scope.next_step = function(){
						window.location='.';
					}
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
					 Ch_pwd: function($params){
			       return $http({
			         method:	'POST',
			         url:'api/index.php/lost-password',
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
							url:'api/index.php/user/verif/' + $params.token,
							header:{'Content-Type':'application/json'}
						}).then(function(response){
							return response.data;
						})
					},
					Update: function($params){
						return $http({
							method:	'PUT',
							url:'api/index.php/user/' + $params.user_id,
							header:{'Content-Type':'application/json'},
							data: $params
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
