(function() {
	"use strict";

	var currentModule = function() {
		return {
			templateUrl: "/app/backend/languages/tpl-list.html",
			controller: "LanguagesController"
		}
	};

	angular.module("backendApp")
		.directive("currentModule", currentModule);

})();
