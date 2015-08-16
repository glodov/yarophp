<?php

include(dirname(__DIR__) . '/includes/application.php');

Application::run('config', 'database');

$login = isset($argv[1]) ? $argv[1] : null;
$password = isset($argv[2]) ? $argv[2] : null;
$level = isset($argv[3]) ? $argv[3] : 1;

if (!$login)
{
	exit("Commang usage: php cli/" . basename(__FILE__) . " LOGIN [PASSWORD] [LEVEL]\n");
}
if (!$password)
{
	$password = substr(mda5(rand(1, 100000) . microtime(true) . date()), 0, 5);
}
$User = Model\User::create($login, $password, $level);
if ($User)
{
	if ($User->save())
	{
		exit("User successfully added\n\tLogin: $login\n\tPassword: $password\n\tLevel: $level\n");
	}
	else
	{
		exit("Cannot save user in database\n");
	}
}
else
{
	exit("User with the same login already exists\n");
}
