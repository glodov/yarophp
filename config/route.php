<?

\Core\Config::set([
	'route' => [
		'/risks-admin'    => 'Controller\\Backend',
		'/'               => 'Controller\\Frontend'
	],
	'default@route' => 'Controller\\Frontend'
]);
