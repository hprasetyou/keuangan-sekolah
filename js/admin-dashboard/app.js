var app= angular.module('admin-dashboard',['ngRoute']);

app.config(function($httpProvider) {
    //Enable cross domain calls
    $httpProvider.defaults.useXDomain = true;
});

app.config(['$routeProvider',function($routeProvider){
  $routeProvider

    .when('/',{
        templateUrl : 'partial/admin-dashboard/home.html',
        controller  : 'home'
    })
    .when('/user',{
        templateUrl : 'partial/admin-dashboard/user.html',
        controller  : 'user'
    })
    .when('/request',{
        templateUrl : 'partial/admin-dashboard/request.html',
        controller  : 'request'
    })
}]);

app.controller('home',['$scope',
    function($scope){

    }]);

app.controller('user',['$scope','User',
    function($scope,User){
      User.Show().then(function(response){
        $scope.alluser=response.data
      })
    }]);


app.controller('request',['$scope','Sekolah',
    function($scope,Sekolah){
      Sekolah.Show_belum_verifikasi().then(function(response){
        $scope.sekolah_belum_verifikasi=response
      })
      $scope.progress={}
      $scope.progress.detail=[]
      console.log(localStorage.getItem('token'));
      $scope.verifikasi= function(){
        Sekolah.Verifikasi($scope.sekolah).then(function(response){
          $scope.progress.detail.push({"task":"ubah status"})
          $scope.progress.value='30'
        })
        Sekolah.Create_db($scope.sekolah).then(function(response){
          $scope.progress.detail.push({"task":"buat database"})
          $scope.progress.value='60'

        })
        Sekolah.Create_table($scope.sekolah).then(function(response){
          $scope.progress.detail.push({"task":"selesai"})
          $scope.progress.value='100'
        })

      }
    }]);


//====__________===_________==============================//
//___/         /__/

    app.factory("User", ['$http', function($http) {
       return {
         Show: function(){
           return $http({
             method:	'GET',
             url:'api/index.php/user'
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