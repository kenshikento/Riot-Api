var ajaxExample = angular.module('leagueoffeeds', []);

ajaxExample.controller('mainController',function($scope,$http){
    $scope.people;
    $scope.friends;
	$scope.regionlist =[{name:"BR",region:"BR1"},
	{name:"EUNE",region:"EUN1"},
	{name:"EUW",region:"EUW1"},
	{name:"JP",region:"JP1"},
	{name:"KR",region:"KR"},
	{name:"LAN",region:"LA1"},
	{name:"LAS",region:"LA2"},
	{name:"NA",region:"NA1"},
	{name:"OCE",region:"OC1"},
	{name:"TR",region:"TR1"},
	{name:"RU",region:"RU"},
	{name:"PBE",region:"PBE1"}];
	$scope.searchResult = [];
    $scope.addPerson = function() {
			console.log($scope.summonerregion);
			
			$scope.searchResult.push($scope.summonname);
			
          $http({					
               method: 'POST',
               url:  '/Riot/riotloader.php',
               data: {summonname: $scope.summonname,summonerregion:$scope.summonerregion}

          }).then(function (response) {// on success
            $scope.friends  =  response.data;
			console.log($scope.friends);

            var arr =[];
			var info = [];
			var infoname = [];
            for (var i = 0; i<10;i++)
                {		
	
              arr.push(
			[
			$scope.friends.spellID[i],	
			$scope.friends.spellID2[i],	
			$scope.friends.champID[i],					
			$scope.friends.champinfo[i]['name'],
			$scope.friends.champinfo[i]['solotier'],
			$scope.friends.champinfo[i]['sololeaguepoints'],
			$scope.friends.champinfo[i]['tier'],
			$scope.friends.champinfo[i]['leaguepoints']]
			  );
	  
                }
				$scope.myArray = arr;
            
          console.log($scope.myArray);
		 
     
          }, function (response) {


              console.log("wrong");
          });
    };
});
