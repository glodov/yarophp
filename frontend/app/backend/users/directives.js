(function() {
	"use strict";

	var currentModule = function() {
		return {
			templateUrl: "/app/backend/users/tpl-list.html",
			controller: "UsersController"
		}
	};

	angular.module("backendApp")
		.directive("currentModule", currentModule);

})();
