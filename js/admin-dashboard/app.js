var app= angular.module('admin-dashboard',['ngRoute']);

app.config(function($httpProvider) {
    //Enable cross domain calls
    $httpProvider.defaults.useXDomain = true;
});

app.config(['$routeProvider',function($routeProvider){
  $routeProvider

    .when('/',{
        templateUrl : 'partial/admin-dashboard/request.html',
        controller  : 'request'
    })
    .when('/user',{
        templateUrl : 'partial/admin-dashboard/user.html',
        controller  : 'user'
    })
    .when('/request',{
        templateUrl : 'partial/admin-dashboard/request.html',
        controller  : 'request'
    })
    .when('/kelola-admin',{
        templateUrl : 'partial/admin-dashboard/kelola-admin.html',
        controller  : 'kelola-admin'
    })
    .when('/setting',{
        templateUrl : 'partial/admin-dashboard/setting.html',
        controller  : 'setting'
    })
}]);


app.controller('user',['$scope','User',
    function($scope,User){
      var tampil = function(){
        User.Show().then(function(response){
          $scope.alluser=response.data
        })
      }
      tampil();
      $scope.nonaktifkan = function(id){
        var data = {
          user_id : id,
          status : "2"
        }
        User.Update(data).then(function(){
          tampil();
        })
      }

      $scope.aktifkan = function(id){
        var data = {
          user_id : id,
          status : "1"
        }
        User.Update(data).then(function(){
          tampil();
        })
      }
    }]);

    app.controller('home',['$scope',
        function($scope){

        }]);

    app.controller('setting',['$scope',
        function($scope){
          $scope.Ch_pwd = function(){
          }

        }]);
app.controller('request',['$scope','Sekolah',
    function($scope,Sekolah){
      Sekolah.Show_belum_verifikasi().then(function(response){
        $scope.sekolah_belum_verifikasi=response
      })
      $scope.progress= {}

      $scope.progress.detail=[]
      console.log(localStorage.getItem('token'));
      $scope.verifikasi= function(index){
        $scope.progress={}
        $scope.progress.detail.push({"task":"verifikasi"})
        $scope.progress.value='10';
        Sekolah.Verifikasi($scope.sekolah).then(function(response){

          $scope.progress.detail.push({"task":"ubah status"})
          $scope.progress.value='20';

          Sekolah.Create_db($scope.sekolah).then(function(response){
            $scope.progress.detail.push({"task":"buat database"})
            $scope.progress.value='60'

            Sekolah.Create_table($scope.sekolah).then(function(response){
              $scope.progress.detail.push({"task":"selesai"})
              $scope.progress.value='100'
              $scope.sekolah_belum_verifikasi.splice(index, 1);
            })
          })
        })
      }
    }]);


    app.controller('kelola-admin',['$scope','User',
        function($scope,User){
          $scope.frm_admin = {}
          $scope.frm_admin.password = "$scope.frm_admin.email"
          $scope.frm_admin.user_level = "1"
          $scope.frm_admin.user_group = "admin"

          User.All_admin().then(function(response){
            $scope.admins = response.data;
          })

          $scope.addadmin = function(){
            $scope.frm_admin.display_name = $scope.frm_admin.email
            $scope.frm_admin.password = "$scope.frm_admin.email"
            $scope.frm_admin.user_level = "1"
            $scope.frm_admin.user_group = "admin"
            $("#loading").modal('show');

            User.Add($scope.frm_admin).then(function(response){
              $("#loading").modal('hide');
              $("#ModalSuccess").modal('show');
              $scope.admins.push($scope.frm_admin)
            })
          }
        }]);


//==========================================================================================
//=====__________=====______===========__________======_________________===//
//=====__________====_________=======_____=====___=====____==______==___===___===//
//=====_____========____===____======____====================_____========//
//=====__________==____=====____=====____====================_____========================//
//=====_____=======_____________=====____====================_____=========================//
//=====_____=======____=====____======____=====___===========_____=========================//
//=====_____=======____=====____========_________============_____===============//
//===========================================================================================



    app.factory("User", ['$http', function($http) {
       return {
         Show: function(){
           return $http({
             method:	'GET',
             url:'api/index.php/user/user_level=2/filter'
           }).then(function(response){
             return response.data;
           })
         },
         Session: function(){
           return $http({
             method:	'GET',
             url:'api/index.php/_session'
           }).then(function(response){
             return response;
           })
         },
         Update: function($params){
           return $http({
             method:	'PUT',
             url:'api/index.php/user/'+ $params.user_id,
             header:{'Content-Type':'application/json'},
             data: $params
           }).then(function(response){
             return response.data;
           })
         },
         Add: function($params){
           return $http({
             method:	'POST',
             url:'api/index.php/user',
             header:{'Content-Type':'application/json'},
             data: $params
           }).then(function(response){
             return response.data;
           })
         },
         All_admin: function(){
           return $http({
             method:	'GET',
             url:'api/index.php/user/user_level=1/filter'
           }).then(function(response){
             return response.data;
           })
         }
       }


       }]);


     app.factory("Sekolah", ['$http', function($http) {
        return {
          Show_belum_verifikasi: function(){
            return $http({
              method:	'GET',
                url:'api/index.php/sekolah/status=0/filter'
            }).then(function(response){
                return response.data;
            })
          },
          Verifikasi: function($params){
            return $http({
              method:	'PUT',
              data: JSON.stringify({
                status:'1'
              }),
                url:'api/index.php/sekolah/'+$params.group_id
            }).then(function(response){
                return response.data;
            })
          },
          Create_db: function($params){
            return $http({
              method:	'POST',
              data: JSON.stringify({
                status:'1'
              }),
                url:'api/index.php/sekolah/'+$params.group_id+ '/create_db'
            }).then(function(response){
                return response.data;
            })
          },
          Create_table: function($params){
            return $http({
              method:	'POST',
              data: JSON.stringify({
                status:'1'
              }),
                url:'api/index.php/sekolah/'+$params.group_id + '/create_tables'
            }).then(function(response){
                return response.data;
            })
          },


      }

      }]);
