<?

date_default_timezone_set('Europe/London');

\Helper\Date::setDefaultFormat('d.m.y');

\Core\Config::set([
	'timezone'	=> 'Europe/London',
]);