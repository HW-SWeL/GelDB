
(function () {
    'use strict';
var gelApp = angular.module("gelApp", ['ngMaterial', 'ngRoute']);/*.controller("gelController", gelController);*/

/*function gelController ($scope, $route) {
    $scope.currentNavItem = 'page1.html';
    $scope.props = ['Chemical name', 'Hansen Value', 'Solvents Used'];

    $scope.$route = $route;

  };*/

gelApp.factory('Data', function(){
  return { dat: {},
           chem: '',
           img: '',
            flag: ''};
});

gelApp.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
  $locationProvider.html5Mode(true);
        $routeProvider
        .when('/Home', { templateUrl : 'home.html', controller: 'gelController', activetab: 'home'})
        .when('/Result', { templateUrl: 'results.html', controller: 'gelController', activetab: 'about'})
        .when('/Enter', {templateUrl: 'enter.html', controller: 'gelController'})
        .when('/SelectChem', {templateUrl: 'SelectChem.html', controller: 'gelController'})
        .otherwise({ redirectTo: '/Home', css: 'gelsStyle.css'});

    }]);

    gelApp.controller( 'gelController', ['$scope', '$route', '$location', '$http', '$mdDialog', 'Data', function($scope, $route, $location, $http, $mdDialog, Data){
      $scope.currentNavItem = 'page1.html';
      $scope.gelSearch = '1-[(1S)-1-Phenylethyl]-3-[2-({[(1S)-1-phenylethyl]carbamoyl}amino)ethyl] urea';
      $scope.route = $route;
      $scope.user = '';
      $scope.pass = '';
      $scope.Data = Data;

      $scope.insertBindings = {
        properties:[{id:'gelName', place: 'Chemical Name', value: ''}, {id: 'InChI', place: 'InChI Key', value: ''}, {id:'molecularFormula', place: 'Molecular Formula', value: ''}, {id: 'morphology', place: 'Morphology', value: '' },
              {id: 'triggerMechanism', place: 'Trigger Mechanism', value: ''}]
      };

      //Values are be bound to these before constructing an INSERT DATA / DELETE INSERT query
      $scope.reactionInsertBindings = {
        properties:[{id:'solvent', place: 'Solvent Name', value: ''},
                    {id: 'ratio', place: 'Ratio', value: ''},
                    {id: 'cgc', place: 'Critical Gel Concentration', value: '', unit: ''}]
      };


      $scope.propertyNames = [{id: 'InChI', value: $scope.Data.dat.InChI}, {id: 'Molecular Formula', value: $scope.Data.dat.formula}, {id: 'SMILES', value: $scope.Data.dat.SMILES}, {id: 'Morphology', value: $scope.Data.dat.morph},
                              {id: 'Trigger Mechanism', value: $scope.Data.dat.triggerMech}
                              ];
      $scope.reactionProperties = ['Solvent', 'Ratio', 'Critical Gel Concentration', 'Solubility', 'Rheology'];
      $scope.reactionsToAdd = [];
      $scope.reactions = [{solvent: 'Choloroform (CHCl3)', cgc: {value: '0.02', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'None' },
                          {solvent: 'Acetonitrile (MeCN)', cgc: {value: '0.05', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'None' },
                          {solvent: 'Acetone', cgc: {value: '0.05', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'None' },
                          {solvent: 'DMSO:H2O', cgc: {value: '0.04', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'A ratio range of 7:3 to 1:9 results in gel formation' },
                          {solvent: 'EtOH:H2O', cgc: {value: '0.04', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'A ratio range of 7:3 to 1:9 results in gel formation' },
                          {solvent: 'MeOH:H2O', cgc: {value: '0.04', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'A ratio range of 7:3 to 1:9 results in gel formation' },
                          {solvent: 'EthylAcetate', cgc: {value: '0.03', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'None' },
                          {solvent: 'Tetrahydrofuran (THF)', cgc: {value: '0.05', unit: 'mM'}, solubility: 'test', rheology: 'other', notes: 'None' }
                          ];

    $scope.updChem = {
      'chem': '', 'InChI': '', 'name': '', 'formula': '', 'SMILES': '', 'morph': '', 'triggerMech': '', 'reactions': []
    };

    $scope.upd = {
      'reaction': '', 'hasSolventName': '', 'hasSolventSMILES': '', 'hasCGC': '', 'unit': '', 'notes': ''
    };

      $scope.go = function( path ){
        $location.path( path );
      };

      $scope.updateChem = function(){
          //alert(JSON.stringify($scope.Data.dat));

          for(var prop in $scope.updChem){
            if($scope.updChem[prop] == ''){
              $scope.updChem[prop] = $scope.Data.dat[prop];
            }
          }

          var data = $scope.updChem;
          $http.post('update.php', data).
            success(function(response){
              alert(response);
              $scope.updChem = null;
              //Probably should reload the page now that it's been updated
            }).error(function(){
              alert("Update Failed");
            });
      };

      $scope.updateReaction = function(){
        //$scope.upd['original'] = Data.dat.solventReaction.hasSolvent.name;

        alert(JSON.stringify($scope.upd));
        /*alert(JSON.stringify($scope.testVar));*/

        for(var prop in $scope.upd){
          //alert(prop + ': ' + $scope.upd[prop])
          if($scope.upd[prop] == ''){
            if(prop == 'hasSolventName'){
              //alert($scope.testVar.hasSolvent.name);
              $scope.upd.hasSolventName = $scope.testVar.hasSolvent.name;
            }else if(prop == 'hasSolventSMILES'){
            //alert($scope.testVar.hasSolvent.SMILES);
            $scope.upd.hasSolventSMILES = $scope.testVar.hasSolvent.SMILES;
          }else{
            //alert($scope.testVar[prop]);
            $scope.upd[prop] = $scope.testVar[prop];
          };
          }
        }

       var data = $scope.upd;
        //alert(JSON.stringify($scope.upd));
        $http.post('updateReaction.php', data).
          success(function(response){
            alert("Succesfully updated Reaction" + response);
            $scope.upd = null;
            $scope.testVar = null;
          }).error(function(){
            alert("update failed");
          });
      };

      $scope.insertReaction = function(){
        var data = {
          'reactionProps' : $scope.reactionInsertBindings
        };
        $http.post('insertReaction.php', data).
          success(function( response ){
            $scope.reactionsToAdd.push(response);
            alert($scope.reactionsToAdd);//return reaction name to give to the other form?
          }).error(function(){
            alert("request failed");
          });
      };

      $scope.loadGel = function(gelObj){
          $location.path('/Result');
          $scope.Data.dat = gelObj;
          alert(JSON.stringify($scope.Data.dat));
      }

      $scope.insertDB = function(){

        $http.get('getStats.php').
          success(function( response ){
            var data = {
              //'ss' : $scope.searchString,
              //'numGels' : response.results.bindings[0].count.value,
              'gelProps' : $scope.insertBindings,
              'reactions' : $scope.reactionsToAdd //Remember to empty this list at the end
              //'reactProps' : $scope.reactionInsertBindings <-- change this to the reactions gel has somehow
            };
          $http.post('insert.php', data).
          success(function( response ){
            alert("Successfully added chemical to database");
            $scope.reactionsToAdd.length = 0;
          }).error(function(){
            $scope.reactionsToAdd.length = 0;
            alert("request failed. Please enter all details again")
          });
        }).error(function(){
          alert("Could not get number of gels");
        });
    };

      $scope.testFunct = function(){
        alert(JSON.stringify($scope.insertBindings));
        alert(JSON.stringify($scope.reactionInsertBindings));
      }

      var openFromLeft = function() {
        $mdDialog.show(
          $mdDialog.prompt()
            .clickOutsideToClose(true)
            .title('Sign in to GelDB')
            .textContent('Please enter the password for your ORCiD account:')
            .placeholder("Password")
            .ok('Sign In')
            .cancel('Cancel')
            // You can specify either sting with query selector
            .openFrom('#test')
            // or an element
            .closeTo(angular.element(document.querySelector('#test')))
        ).then(function(result){
          if(result != undefined){
            $scope.pass = result;
            alert($scope.user + $scope.pass);
          }else{
            openFromLeft();
          }
        });
      };

      $scope.searchDB = function(/*dat*/){
        //$location.path( '/Result' );
        var data = {
          'ss' : $scope.searchString
        };
        $http.post('search.php', data).
        success(function( response ){
          if(response.flag == 'name'){
            alert(JSON.stringify(response));
            $scope.Data.img = response.structure;
            //alert($scope.Data.img);
            $scope.Data.dat = response.result.items[0];
            $location.path( '/Result' );
            //alert(JSON.stringify($scope.Data.dat));
          }else if(response.flag == 'solvent'){ //Can probable use this page for other searches too e.g hansen value
              alert( JSON.stringify(response) );
              $scope.Data.flag = response.flag;
              $scope.Data.dat = response.result.items;
              $scope.Data.chem = data.ss;
              $location.path( '/SelectChem' );
          }
          //alert( JSON.stringify(response) );
        }).error(function(){
          alert("request failed")
        });
      };

      $scope.showPrompt = function(ev) {
      // Appending dialog to document.body to cover sidenav in docs app
      var confirm = $mdDialog.prompt()
            .title('Sign in to GelDB')
            .clickOutsideToClose(true)
            .textContent('To enter or update records, you must sign in. Use your existing ORCiD to do so.')
            .placeholder('Email Address')
            .targetEvent(ev)
            .ok('Continue')
            .cancel('Cancel');

      $mdDialog.show(confirm).then(function(result) {
        if(result != undefined){
          openFromLeft();
          $scope.user = result;
        }else{
          $scope.showPrompt();
        }
      });
      };
      function DialogController($scope, $mdDialog) {

        $scope.hide = function() {
          $mdDialog.hide();
        };

        $scope.cancel = function() {
          $mdDialog.cancel();
        };

        $scope.answer = function(answer) {
          $mdDialog.hide(answer);
        };
      }

    }]);

    gelApp.controller( 'homeController', ['$scope', '$route', '$location', '$http', '$mdDialog', 'Data', function($scope, $route, $location, $http, $mdDialog, Data){

      $scope.props = ['Chemical name,', 'InChI key,', 'Solvents Used'];
      //$scope.showConfirm = showConfirm;
      $scope.user = '';
      $scope.pass = '';
      $scope.Data = Data;
      $scope.go = function( path ){
        $location.path( path );
      };

      var init = function(){
        $http.get('getStats.php').
          success(function( response ){
            $scope.numGels = response.results.bindings[0].count.value;
          }).error(function(){
            alert("request failed");
          });
      };

      init();

      var openPassword = function() {
        $mdDialog.show(
          $mdDialog.prompt()
            .clickOutsideToClose(true)
            .title('Sign in to GelDB')
            .textContent('Please enter the password for your ORCiD account:')
            .placeholder("Password")
            .ok('Sign In')
            .cancel('Cancel')
            // You can specify either sting with query selector
            .openFrom('#test')
            // or an element
            .closeTo(angular.element(document.querySelector('#test')))
        ).then(function(result){
          if(result != undefined){
            $scope.pass = result;
            alert($scope.user + $scope.pass);
          }else{
            openPassword();
          }
        });
      };

      $scope.showPrompt = function(ev) {
      // Appending dialog to document.body to cover sidenav in docs app
      var confirm = $mdDialog.prompt()
            .title('Sign in to GelDB')
            .clickOutsideToClose(true)
            .textContent('To enter or update records, you must sign in. Use your existing ORCiD to do so.')
            .placeholder('Email Address')
            .targetEvent(ev)
            .ok('Continue')
            .cancel('Cancel')
            .openFrom('#test')
            // or an element
            .closeTo(angular.element(document.querySelector('#test')));

      $mdDialog.show(confirm).then(function(result) {
        if(result != undefined){
          $scope.user = result;
          openPassword();
        }else{
          $scope.showPrompt();
        }
      });
    };
      function DialogController($scope, $mdDialog) {

        $scope.hide = function() {
          $mdDialog.hide();
        };

        $scope.cancel = function() {
          $mdDialog.cancel();
        };

        $scope.answer = function(answer) {
          $mdDialog.hide(answer);
        };
      }

      $scope.testReq = function(){
        $location.path('/Enter');
      }

      $scope.searchDB = function(){
        //$location.path( '/Result' );
        var data = {
          'ss' : $scope.searchString
        };
        $http.post('search.php', data).
        success(function( response ){
          if(response.flag == 'name'){
            alert(JSON.stringify(response));
            $scope.Data.dat = response.result.items[0];
            $scope.Data.img = response.structure;
            $location.path( '/Result' );
            //alert(JSON.stringify($scope.Data.dat));

          }else if(response.flag == 'solvent'){ //Can probable use this page for other searches too e.g hansen value
              alert( JSON.stringify(response) );
              $scope.Data.flag = response.flag;
              $scope.Data.dat = response.result.items;
              $scope.Data.chem = data.ss;
              $location.path( '/SelectChem' );
          }
          //alert( JSON.stringify(response) );
        }).error(function(){
          alert("request failed")
        });
      };

    }]);

})();
