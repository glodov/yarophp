(function() {
	"use strict";

	var LoginController = function($scope, Locale, Request, $http, $location) {
		Locale.load($scope);

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

			Request.post("login", {model: $scope.model})
				.then(function(data) {
					if (data.result && data.token)
						window.location = window.backendModuleUrl;
					else if (data.error)
						alert(data.error);
				});
		};
	};

	angular.module("loginApp")
		.controller("LoginController", ["$scope", "Locale", "Request", "$http", "$location", LoginController]);

})();
