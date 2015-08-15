<!DOCTYPE html>
<html lang="@todo language">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<title><?= htmlspecialchars($Webpage->seo->title) ?></title>
<? if ($Webpage->seo->keywords) : ?>
		<meta name="keywords" content="<?= htmlspecialchars($Webpage->seo->keywords) ?>" />
<? endif; ?>
<? if ($Webpage->seo->description) : ?>
		<meta name="description" content="<?= htmlspecialchars($Webpage->seo->description) ?>" />
<? endif; ?>

<!-- @todo favicon for desktop/mobile/iOS/ms -->
<!-- @todo og meta -->
<!-- @todo fb meta -->
<!-- @todo twitter meta -->

<? foreach ($this->getCSS() as $file => $media) : ?>
		<link rel="stylesheet" href="<?= $file ?>" media="<?= $media ?>">
<? endforeach; ?>

<!-- TRACKER HEAD -->
	</head>

	<body>

<!-- CONTENT -->

<? foreach ($this->getScripts() as $file => $type) : ?>
		<script type="<?= $type ?>" src="<?= $file ?>"></script>
<? endforeach; ?>
	</body>

</html>
