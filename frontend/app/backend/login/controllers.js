(function() {
	"use strict";

<<<<<<< HEAD
	var LoginController = function($scope, Locale, Request, $http, $location) {
		Locale.load($scope);
=======
	var LoginController = ["$scope", "localeFactory", "$http", "$location", function($scope, localeFactory, $http, $location) {
		localeFactory.add(["Backend"]);
		localeFactory.load($scope);
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

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

<<<<<<< HEAD
			Request.post("login", {model: $scope.model})
				.then(function(data) {
=======
			$http.post(window.backendModuleUrl + '/login', {model: $scope.model})
				.success(function(data) {
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
					if (data.result && data.token)
						window.location = window.backendModuleUrl;
					else if (data.error)
						alert(data.error);
<<<<<<< HEAD
				});
		};
	};

	angular.module("loginApp")
		.controller("LoginController", ["$scope", "Locale", "Request", "$http", "$location", LoginController]);
=======
				})
				.error(function(data) {

				});
		};
	}];

	angular.module("loginApp")
		.controller("LoginController", LoginController);
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

})();
