(function() {

	var sidebar = function() {
		return {
			templateUrl: "/app/backend/default/tpl-sidebar.html",
			controller: "SidebarController",
			link: function($scope, $element, $attrs) {
				var body = document.body, html = document.documentElement;
				var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
				angular.element($element).height(height);
			}
		};
	};

	var topbar = function() {
		return {
			templateUrl: "/app/backend/default/tpl-topbar.html",
			controller: "TopbarController"
		};
	};

	var activeIcon = function() {
		return {
			require: "ngModel",
			scope: {
				value: "=ngModel"
			},
			template: '<i class="fa" ng-class="class()"></i>',
			controller: ["$scope", function($scope) {
				$scope.class = function() {
					var value = ("" + $scope.value).toLowerCase();
					return {
						'fa-dot-circle-o': value === '1' || value === 'true',
						'fa-circle-o': value === '0' || value === 'false' || value === "null" || value === ""
					};
				};
			}]
		}
	};

	var loadingIcon = function(Locale) {
		return {
			scope: {
				value: "="
			},
			template: '<span ng-show="visible()"><i class="fa fa-spin fa-circle-o-notch"></i></span>',
			controller: function($scope, $element, $attrs) {
				$scope.loading = Locale.get("LOADING");
				$scope.visible = function() {
					var value = ("" + $scope.value).toLowerCase();
					return value === '1' || value === 'true';
				};
			}
		}
	};

	var validation = function() {
		var validations = [
			"password",
			"confirm",
			"phone",
			"email",
			"async"
		];

		function getFormGroupElement($input, className) {
			className = className || 'form-group';
			var $fg = null, $p = $input.parent();
			while ($p && $p.get(0) !== undefined && $p.get(0).tagName != "BODY" && $p.get(0).tagName != "HTML"
				&& $p.get(0).tagName !== undefined) {
					if ($p.hasClass(className)) {
						$fg = $p;
						break;
					}
					$p = $p.parent();
				}
			return $fg;
		}

		function updateFormGroup(form, $input, errors) {
			errors = errors || [];
			var $div = getFormGroupElement($input);
			if (null === $div || !$div.size())
				return false;
			$div.removeClass('has-warning has-error has-success');

			var name = angular.element($input).attr('name'), input = form[name],
				$p = getFormGroupElement($input, 'validation-wrap') || $input.parent();
			// console.log('Checking ', name + ": " + input.$viewValue);
			if (input.$touched || input.$dirty || form.$submitted) {
				$div.find('.validation-error').remove();
				$p.find('.validation-icon').remove();
				if (input.$invalid) {
					$div.addClass('has-error');
					var hasError = false;
					angular.forEach(errors, function(error) {
						var $err = angular.element('<div class="validation-error"></div>');
						$err.text(error);
						$div.append($err);
						hasError = true;
					});
					$p.append('<span class="validation-icon validation-icon-error"><i class="fa fa-exclamation-triangle text-danger"></i></span>');
				}
				if (input.$valid) {
					$div.addClass('has-success');
					$p.append('<span class="validation-icon validation-icon-success"><i class="fa fa-check text-success"></i></span>');
				}
				if (input.$pending) {
					$div.addClass('has-pending');
					$p.append('<span class="validation-icon validation-icon-pending"><i class="fa fa-spin fa-spinner"></i></span>');
				}
			}
		}

		function getErrors($input, input) {
			var errors = [];
			if ($input.attr('validation-errors')) {
				var json = $input.attr('validation-errors').replace(/'/g, '"');
				errors = angular.fromJson(json);
			}
			var result = [];
			angular.forEach(errors, function(value, name) {
				if (input.$error[name])
					result.push(value);
			});
			return result;
		}

		return {
			require: "ngModel",
			link: function($scope, $element, $attrs, ngModel) {
				var $form = angular.element($element[0].form),
					formName = angular.element($element[0].form).attr('name'),
					form = $scope[formName];

				angular.forEach($attrs, function(value, name) {
					if ('$' != name.substring(0, 1)) {

					}
				});

				$form.on('submit', function() {
					angular.forEach(form, function(input, name) {
						if (name.substring(0, 1) != '$') {
							input.$setDirty();
							var $input = $form.find('[name="' + name + '"]');
							updateFormGroup(form, $input, getErrors($input, input));
						}
					})
					// console.log('Directive submit', form.$invalid);
					if (form.$invalid)
						return false;
				});

				$scope.$watch($attrs.ngModel, function(newValue, oldValue) {
					if (newValue != oldValue){
						var $input = angular.element($element), name = $input.attr('name');
						updateFormGroup(form, $element, getErrors($input, form[name]));
					}
				});
			}
		};
	};

	angular.module("backendApp")
		.directive("sidebar", sidebar)
		.directive("topbar", topbar)
		.directive("activeIcon", activeIcon)
		.directive("loadingIcon", ["Locale", loadingIcon])
		.directive("validation", validation);
})();
