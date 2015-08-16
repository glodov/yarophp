(function(){
	"use strict";

	var localeFactory = function($http) {
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
			$http.get(path)
				.success(function(data) {
					loaded[path] = data;
					angular.extend(dictionary, loaded[path]);
					callback.call();
				})
		};

		return {
			add: addModule,

			load: function($scope, locale, fn) {
				scope = $scope;
				locale = locale || "en_US";
				fnCallback = fn;
				angular.forEach(modules, function(module) {
					var path = "/i18n/" + locale + "/" + module + ".json";
					loadItem(path);
				});
			}
		};
	};

	angular.module("default", [])
		.factory("localeFactory", ["$http", localeFactory]);

})();
