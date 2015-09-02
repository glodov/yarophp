(function(){
	"use strict";

<<<<<<< HEAD
	var Locale = function($http) {
=======
	var localeFactory = function($http) {
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		var loaded = {}, modules = [], queue = [], dictionary = {}, fnCallback = null, scope = null;

		var addModule = function(module) {
			if (typeof module !== "object") module = [module];
			for (var i = 0; i < module.length; i++) {
				var item = module[i];
				if (modules.indexOf(item) == -1) {
					modules.push(item);
				}
			}
		};

		var callback = function() {
			if (queue.length)
				queue.pop();
			if (queue.length == 0) {
				scope.t = dictionary;
				if (fnCallback)
					fnCallback.call(this, dictionary);
			}
		};

		var loadItem = function(path) {
			queue.push(path);
			if (typeof loaded[path] !== "undefined") {
				angular.extend(dictionary, loaded[path]);
				callback.call();
				return true;
			}
<<<<<<< HEAD
			// if (typeof loading[path] !== "undefined")
			// 	return true;
			// loading[path] = true;
			$http.get(path)
				.success(function(data) {
					loaded[path] = data;
					// loading[path] = true;
=======
			$http.get(path)
				.success(function(data) {
					loaded[path] = data;
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
					angular.extend(dictionary, loaded[path]);
					callback.call();
				})
		};

		return {
			add: addModule,

<<<<<<< HEAD
			get: function(word) {
				if (typeof dictionary[word] == "undefined")
					return "*" + word + "*";
				else
					return dictionary[word];
			},

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
			load: function($scope, locale, fn) {
				scope = $scope;
				locale = locale || "en_US";
				fnCallback = fn;
<<<<<<< HEAD
				addModule("Backend");
				if (window.backendModuleI18n)
					addModule(backendModuleI18n);
=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
				angular.forEach(modules, function(module) {
					var path = "/i18n/" + locale + "/" + module + ".json";
					loadItem(path);
				});
			}
		};
	};

<<<<<<< HEAD
	var Request = function($q, $http, $rootScope) {
		var post = function(link, data) {
			var deferred = $q.defer();
			data = data || {};
			angular.extend(data, {"ajax": true});

			var url = "/" == link.substring(0, 1) ? window.backendRootUrl : (window.backendModuleUrl + "/");
			url += link;
			$rootScope.loading = true;
			$http.post(url, data)
				.success(function(data) {
					$rootScope.loading = false;
					deferred.resolve(data);
				})
				.error(function(data) {
					$rootScope.loading = false;
					if (data.error)
						alert(data.error);
					else
						alert("Error to send AJAX response: " + link);
					if (data.redirect)
						window.location = data.redirect;
					deferred.reject(data);
				});

			return deferred.promise;
		};

		return {
			post: post
		};
	};

	var I18n = function() {
		return {
			copy: function(model) {
				var result = angular.copy(model);
				angular.forEach(model.i18n, function(item, locale) {
					angular.forEach(item, function(value, key) {
						if (typeof model[key] == "number")
							result.i18n[locale][key] = parseFloat(value);
					});
				});
				return result;
			}
		};
	};

	angular.module("default", [])
		.factory("Locale", ["$http", Locale])
		.factory("Request", ["$q", "$http", "$rootScope", Request])
		.factory("I18n", I18n);
=======
	angular.module("default", [])
		.factory("localeFactory", ["$http", localeFactory]);
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

})();
