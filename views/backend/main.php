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
			window.backendRootUrl = "<?= _url(new \Controller\Backend) ?>";
			window.backendModuleUrl = "<?= _url($this->getController()) ?>";
			window.backendModuleI18n = "<?= strtr(get_class($this->getController()), ['Controller\\' => '', '\\' => '_']) ?>";
		</script>

	</head>

	<body ng-app="backendApp">

		<div class="container-fluid container-block" ng-controller="MainController">

			<div class="topbar-nav" topbar></div>
			<div class="sidebar-nav" sidebar></div>
			<div class="main-content container-fluid">
				<!-- CONTENT -->
				<current-module></current-module>
			</div>
		</div>

<? foreach ($this->getScripts() as $file => $type) : ?>
		<script type="<?= $type ?>" src="<?= $file ?>"></script>
<? endforeach; ?>
	</body>

</html>
