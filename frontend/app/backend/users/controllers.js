(function() {
	"use strict";

	var UsersController = function($rootScope, $scope, Request, $modal) {
		var itemTemplate = {}, languages = [];
		$scope.list = [];

		var load = function() {
			Request.post("json")
				.then(function(data){
					$scope.list = data.list;
					itemTemplate = data.itemTemplate;
					languages = data.languages;
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
				templateUrl: "/app/backend/users/tpl-form.html",
				controller: "UserFormController",
				windowClass: "user-modal",
				resolve: {
					model: function() {
						return item;
					},
					languages: function() {
						return languages;
					}
				}
			}).result.then(function(item) {
				// save item here
				save(item);
			}, function() {
				// canceled
			});
		};

		$scope.getLanguage = function(locale) {
			var result = null;
			angular.forEach(languages, function(item) {
				if (null === result && item.locale == locale)
					result = item;
			});
			if (null !== result)
				return result.name;
			return result;
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

		};

		load();
	};

	var UserFormController = function($scope, $modalInstance, Locale, model, languages) {
		Locale.load($scope);
		$scope.model = angular.copy(model);
		$scope.languages = languages;

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
		.controller("UsersController", ["$rootScope", "$scope", "Request", "$modal", UsersController])
		.controller("UserFormController", ["$scope", "$modalInstance", "Locale", "model", "languages", UserFormController]);
})();
