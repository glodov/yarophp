(function() {
	"use strict";

	var LanguagesController = function($rootScope, $scope, Request, $modal, $http) {
		var itemTemplate = {}, locales = [];
		$scope.list = [];

		$http.get("/app/json/native-locales.json")
			.then(function(response) {
				locales = [];
				angular.forEach(response.data, function(item, locale) {
					item.locale = locale;
					locales.push(item);
				});
			});

		var load = function() {
			Request.post("json")
				.then(function(data){
					$scope.list = data.list;
					itemTemplate = data.itemTemplate;
				});
		};

		var save = function(item) {
			Request.post("save", {model: item})
				.then(function(data) {
					$scope.list = data.list;
				});
		};

		var deleteItem = function(item) {
			Request.post("delete", {model: item})
				.then(function(data) {
					$scope.list = data.list;
				});
		};

		var saveSort = function() {
			var items = [];
			angular.forEach($scope.list, function(item) {
				items.push(item.id);
			});
			Request.post("pos", {items: items});
		};

		var openForm = function(item) {
			$modal.open({
				size: "lg",
				templateUrl: "/app/backend/languages/tpl-form.html",
				controller: "LanguageFormController",
				resolve: {
					model: function() {
						return item;
					},
					locales: function() {
						return locales;
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
			Request.post("check", {model: item})
				.then(function(data) {
					if (data.confirm && window.confirm(data.confirm) || data.none)
						deleteItem(item);
				});
		};

		$scope.sortableOptions = {
			axis: "y",
			"ui-floating": true,
			stop: function() {
				saveSort();
			}
		};

		$rootScope.$watch('website', function(value) {
			load();
		}, true);
	};

	var LanguageFormController = function($scope, $modalInstance, Locale, model, locales) {
		Locale.load($scope);
		$scope.model = angular.copy(model);
		$scope.locales = locales;

		var getLocale = function(locale) {
			var result = null;
			angular.forEach(locales, function(item) {
				if (null === result && item.locale == locale)
					result = item;
			});
			return result;
		};

		$scope.ok = function() {
			if ($scope.form.$invalid)
				return false;
			$modalInstance.close($scope.model);
			return false;
		};

		$scope.cancel = function() {
			$modalInstance.dismiss("cancel");
		};

		$scope.$watch('model.locale', function(locale) {
			var item = getLocale(locale);
			if (item && $scope.form.locale.$dirty)
				$scope.model.name = item.native;
		});
	};

	angular.module("backendApp")
		.controller("LanguagesController", ["$rootScope", "$scope", "Request", "$modal", "$http", LanguagesController])
		.controller("LanguageFormController", ["$scope", "$modalInstance", "Locale", "model", "locales", LanguageFormController]);
})();
