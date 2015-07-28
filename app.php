<?php

include('includes/application.php');

Application::run('config', 'database');

$response = [];
$response['areas'] = \Model\Area::model()->findList([], 'name asc');
$response['risks'] = [];
foreach (\Model\Risk::model()->findList(['parent_id = 0'], 'name asc') as $Risk)
{
	$Risk->children = [];
	foreach (\Model\Risk::model()->findList(['parent_id = '.$Risk->id], 'name asc') as $Child)
	{
		$Risk->children[] = $Child;
	}
	$response['risks'][] = $Risk;
}

echo json_encode($response, JSON_PRETTY_PRINT);
