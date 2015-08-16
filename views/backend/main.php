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

		<div class="container-fluid container-block">

			<div class="row">
				<div class="col-sm-2 col-md-3">
					<div class="sidebar-nav">
						<div class="navbar navbar-default" role="navigation">
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
									<i class="fa fa-bars fa-lg"></i>
								</button>
								<span class="navbar-brand">
									<a class="logo" href="<?= get_url(new \Controller\Backend) ?>">
										<img src="/img/backend-logo.png" alt=""/>
									</a>
								</span>
							</div>
							<div class="navbar-collapse collapse sidebar-navbar-collapse">
								<ul class="nav navbar-nav">
									<li class="active">
										<a href="#">
											<i class="fa fa-dashboard"></i>
											<span class="hidden-sm">Dashboard</span>
										</a>
									</li>
									<li>
										<a href="#">
											<i class="fa fa-file-text-o"></i>
											<span class="hidden-sm">Webpages</span>
										</a>
									</li>
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">
											<i class="fa fa-sitemap"></i>
											<span class="hidden-sm">Dropdown</span> <b class="caret"></b>
										</a>
										<ul class="dropdown-menu">
											<li><a href="#">Action</a></li>
											<li><a href="#">Another action</a></li>
										</ul>
									</li>
									<li class="divider"></li>
									<li>
										<a href="<?= get_url($this->c()) ?>/logout">
											<i class="fa fa-sign-out"></i>
											<span class="hidden-sm">Log out</span>
										</a>
									</li>
									<li><a href="#">Reviews <span class="badge">1,118</span></a></li>
								</ul>
							</div><!--/.nav-collapse -->
						</div>
					</div>
				</div>
				<div class="col-sm-10 col-md-9">
					Main content goes here
				</div>
			</div>
		</div>
<!-- CONTENT -->

<? foreach ($this->getScripts() as $file => $type) : ?>
		<script type="<?= $type ?>" src="<?= $file ?>"></script>
<? endforeach; ?>
	</body>

</html>
