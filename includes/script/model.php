<?php

include(dirname(__DIR__) . '/application.php');

\Application::run('config', 'database');

if (empty($argv[1]) || empty($argv[2]))
{
	echo "Required params: \n";
	echo "\tdatabase table \n";
	echo "\tmodel name\n";
	exit;
}

$table = $argv[1];
$fields = \Application::db()->query('show columns from '.\Application::db()->map($table));
if (!count($fields))
{
	echo "Table [{$argv[1]}] not found\n";
	exit;
}
$name = trim(preg_replace('/[\\\\\/]+/', '/', $argv[2]), '/');
$file = dirname(__DIR__) . '/model/' . strtolower($name) . '.php';
if (file_exists($file))
{
	echo "Model [{$argv[2]}] already exists\n";
	exit;
}

$primary = [];
$ns = explode('/', $name);
$class = array_pop($ns);
array_unshift($ns, 'Model');
$content = "<?\n\n"
		. "namespace ".implode('\\', $ns).";\n\n"
		. "class $class extends \\Core\\Object\n"
		. "{\n\n";

foreach ($fields as $item)
{
	$content .= "\tpublic \${$item['Field']};\n";
	if ('PRI' === $item['Key'])
	{
		$primary[] = "'{$item['Field']}'";
	}
}

$content .= "\n\tpublic function getTableName() { return '$table'; }\n\n"
		. "\tpublic function getPrimary() { return [".implode(',', $primary)."]; }\n\n"
		. "}\n";

$dir = dirname($file); 
if (!file_exists($dir))
{
	mkdir($dir, 0755, true);
}
file_put_contents($file, $content);

echo "Model [$class] succesfully added\n";
