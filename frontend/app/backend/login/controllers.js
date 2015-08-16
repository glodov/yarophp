(function() {
	"use strict";

	var LoginController = ["$scope", "localeFactory", "$http", "$location", function($scope, localeFactory, $http, $location) {
		localeFactory.add(["Backend"]);
		localeFactory.load($scope);

		$scope.model = {
			login: "",
			password: "",
			remember: false
		};

		$scope.inputClass = function(name) {
			return {
				'has-success': $scope.form[name].$valid && ($scope.form[name].$touched || $scope.form.$submitted),
				'has-error': $scope.form[name].$invalid && ($scope.form[name].$touched || $scope.form.$submitted)
			};
		}

		$scope.submit = function() {
			if ($scope.form.$invalid)
				return false;

			$http.post(window.backendModuleUrl + '/login', {model: $scope.model})
				.success(function(data) {
					if (data.result && data.token)
						window.location = window.backendModuleUrl;
					else if (data.error)
						alert(data.error);
				})
				.error(function(data) {

				});
		};
	}];

	angular.module("loginApp")
		.controller("LoginController", LoginController);

})();
