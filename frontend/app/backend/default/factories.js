(function(){
	"use strict";

	var Locale = function($http) {
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
			// if (typeof loading[path] !== "undefined")
			// 	return true;
			// loading[path] = true;
			$http.get(path)
				.success(function(data) {
					loaded[path] = data;
					// loading[path] = true;
					angular.extend(dictionary, loaded[path]);
					callback.call();
				})
		};

		return {
			add: addModule,

			get: function(word) {
				if (typeof dictionary[word] == "undefined")
					return "*" + word + "*";
				else
					return dictionary[word];
			},

			load: function($scope, locale, fn) {
				scope = $scope;
				locale = locale || "en_US";
				fnCallback = fn;
				addModule("Backend");
				if (window.backendModuleI18n)
					addModule(backendModuleI18n);
				angular.forEach(modules, function(module) {
					var path = "/i18n/" + locale + "/" + module + ".json";
					loadItem(path);
				});
			}
		};
	};

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

})();
