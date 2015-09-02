(function() {
	"use strict";

	var currentModule = function() {
		return {
			templateUrl: "/app/backend/webpages/tpl-list.html",
			controller: "WebpagesController"
		}
	};

	angular.module("backendApp")
		.directive("currentModule", currentModule);

})();
