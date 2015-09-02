(function() {
	"use strict";

	var WebpagesController = function($rootScope, $scope, Request, $modal, $http) {
		var itemTemplate = {}, languages = [], controllers = [];
		$scope.list = [];

		var load = function() {
			Request.post("json")
				.then(function(data){
					$scope.list = data.list;
					itemTemplate = data.itemTemplate;
					languages = data.languages;
					controllers = data.controllers;
				});
		};

		var save = function(item) {
			Request.post("save", {model: item})
				.then(function(data) {
					$scope.list = data.list;
				});
		};

		var openForm = function(item) {
			$modal.open({
				size: "lg",
				templateUrl: "/app/backend/webpages/tpl-form.html",
				controller: "WebpageFormController",
				windowClass: "webpage-modal",
				resolve: {
					model: function() {
						return item;
					},
					languages: function() {
						return languages;
					},
					controllers: function() {
						return controllers;
					}
				}
			}).result.then(function(item) {
				// save item here
				save(item);
			}, function() {
				// canceled
			});
		};

		$scope.add = function() {
			openForm(angular.copy(itemTemplate));
		};

		$scope.edit = function(item) {
			openForm(item);
		};

		$scope.enable = function(item) {
			item.is_active = 1 - item.is_active;
			save(item);
		};

		$scope.delete = function(item) {
			Request.post("delete", {model: item})
				.then(function(data) {
					var index = $scope.list.indexOf(item);
					$scope.list.splice(index, 1);
				});
		};

		$scope.themeUrl = function(item) {
			return window.backendModuleUrl + "/theme/" + item.id;
		};

		$scope.sortableOptions = {
			axis: "y",
			"ui-floating": true,
			stop: function() {
				saveSort();
			}
		};

		load();
	};

	var WebpageFormController = function($scope, $modalInstance, Locale, I18n, model, languages, controllers) {
		Locale.load($scope);
		$scope.model = I18n.copy(model);
		$scope.languages = languages;
		$scope.controllers = controllers;

		$scope.ok = function() {
			if ($scope.form.$invalid)
				return false;
			$modalInstance.close($scope.model);
			return false;
		};

		$scope.cancel = function() {
			$modalInstance.dismiss("cancel");
		};
	};

	angular.module("backendApp")
		.controller("WebpagesController", ["$rootScope", "$scope", "Request", "$modal", "$http", WebpagesController])
		.controller("WebpageFormController", ["$scope", "$modalInstance", "Locale", "I18n", "model", "languages", "controllers", WebpageFormController]);
})();
