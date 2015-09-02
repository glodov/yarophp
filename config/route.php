<?

\Core\Config::set([
	'route' => [
		'/admin-custom-url' => 'Controller\\Backend',
		'/'                 => 'Controller\\Frontend'
	],
	'default@route' => 'Controller\\Frontend'
]);
