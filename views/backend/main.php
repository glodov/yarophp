<!DOCTYPE html>
<html lang="@todo language">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
		<title><?= htmlspecialchars($Webpage->seo->title) ?></title>

<!-- @todo favicon for desktop/mobile/iOS/ms -->

<? foreach ($this->getCSS() as $file => $media) : ?>
		<link rel="stylesheet" href="<?= $file ?>" media="<?= $media ?>">
<? endforeach; ?>

<!-- TRACKER HEAD -->
	</head>

	<body>

<!-- CONTENT -->
<?= $this->t('body') ?>

<? foreach ($this->getScripts() as $file => $type) : ?>
		<script type="<?= $type ?>" src="<?= $file ?>"></script>
<? endforeach; ?>
	</body>

</html>
