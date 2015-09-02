(function() {
	"use strict";

	var MainController = function($scope, $http, Locale, Request, $rootScope) {
		Locale.load($scope);

		Request.post("/json")
			.then(function(data) {
				angular.forEach(data, function(value, key) {
					$rootScope[key] = value;
				});
			});
	};

	var SidebarController = function($scope) {
		$scope.menuItemClass = function(item) {
			return {
				'active': item.url == window.backendModuleUrl
			};
		};

		$scope.getLogoutUrl = function() {
			return window.backendModuleUrl + "/logout";
		};

		$scope.getHomeUrl = function() {
			return window.backendRootUrl;
		};
	};

	var TopbarController = function($scope, Request, $rootScope, $timeout) {
		$scope.selectWebsite = function(item) {
			if (item.id == $rootScope.website.id)
				return false;

			Request.post("/json", {website: item})
				.then(function(data) {
					$rootScope.website = data.website;
				});
		};
	};

	angular.module("backendApp")
		.controller("MainController", ["$scope", "$http", "Locale", "Request", "$rootScope", MainController])
		.controller("SidebarController", ["$scope", SidebarController])
		.controller("TopbarController", ["$scope", "Request", "$rootScope", "$timeout", TopbarController]);
})();
