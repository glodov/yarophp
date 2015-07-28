<?php

/*
DROP TABLE IF EXISTS `areas`;
CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `risks`;
CREATE TABLE `risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB CHARACTER SET=utf8 COLLATE=utf8_general_ci;
*/

include('includes/application.php');

Application::run('config', 'database');

$file = isset($argv[1]) ? $argv[1] : null;
if (!$file || !file_exists($file))
{
	echo "File not exists: $file\n";
	exit;
}

$f = fopen($file, 'r');
if (!$f)
{
	echo "Cannot open file: $file\n";
	exit;
}
$current = 0;
while (($row = fgetcsv($f, 200, ';')) !== false)
{
	if ('id' === $row[0])
	{
		$current++;
		continue;
	}
	switch ($current)
	{
		case 1:
			$Area = new \Model\Area();
			$Area->id = trim($row[0]);
			$Area->name = trim($row[2]);
			if ($Area->id && $Area->name)
			{
				$Area->saveNew();
				echo "Area: {$Area->id}, {$Area->name}\n";
			}
			break;

		case 2:
			$Risk = new \Model\Risk();
			$Risk->id = trim($row[0]);
			$Risk->parent_id = trim($row[1]);
			$Risk->name = trim($row[2]);
			if ($Risk->id && $Risk->name)
			{
				$Risk->saveNew();
				echo "Risk: {$Risk->id}, {$Risk->name}\n";
			}
			break;
	}
}
echo "Done\n";
