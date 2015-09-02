<!DOCTYPE html>
<html lang="<?= \Helper\Locale::getPrefix() ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<title><?= htmlspecialchars($Webpage->seo->title) ?></title>

<!-- @todo favicon for desktop/mobile/iOS/ms -->

<? foreach ($this->getCSS() as $file => $media) : ?>
		<link rel="stylesheet" href="<?= $file ?>" media="<?= $media ?>">
<? endforeach; ?>
		<script>
<<<<<<< HEAD
			window.backendRootUrl = "<?= _url(new \Controller\Backend) ?>";
			window.backendModuleUrl = "<?= _url($this->getController()) ?>";
=======
			window.backendBaseUrl = "<?= get_url(new \Controller\Backend) ?>";
			window.backendModuleUrl = "<?= get_url($this->getController()) ?>";
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		</script>

	</head>

	<body ng-app="loginApp">

<!-- CONTENT -->
		<div class="container" ng-controller="LoginController">

			<form class="form-signin" name="form" novalidate ng-submit="submit()">
				<div class="text-center">
<<<<<<< HEAD
					<a class="logo" href="/"><img src="/img/logo.jpg" alt=""/></a>
=======
					<a class="logo" href="/"><img src="/img/backend-logo.png" alt=""/></a>
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
				</div>
				<h2 class="form-signin-heading text-center">{{t.LOGIN_FORM_TITLE}}</h2>
				<div class="form-group" ng-class="inputClass('login')">
					<label for="inputEmail" class="sr-only">{{t.LOGIN}}</label>
					<input type="text" id="inputEmail" class="form-control" placeholder="{{t.PLACEHOLDER_LOGIN}}" name="login" ng-model="model.login" required autofocus>
				</div>
				<div class="form-group" ng-class="inputClass('password')">
					<label for="inputPassword" class="sr-only">{{t.PASSWORD}}</label>
					<input type="password" id="inputPassword" class="form-control" placeholder="{{t.PLACEHOLDER_PASSWORD}}" name="password" ng-model="model.password" required>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" ng-model="model.remember" value="1"> {{t.REMEMBER_ME}}
					</label>
				</div>
				<button class="btn btn-lg btn-primary btn-block" type="submit">{{t.BTN_LOGIN}}</button>
			</form>

		</div>

<? foreach ($this->getScripts() as $file => $type) : ?>
		<script type="<?= $type ?>" src="<?= $file ?>"></script>
<? endforeach; ?>
	</body>

</html>
