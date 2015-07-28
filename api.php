<?php

include('includes/application.php');

Application::run('config', 'database');

use \Helper\Request as Request;

$response = [];

$method = Request::get('method');
$response['method'] = $method;
$response['result'] = false;
switch ($method)
{
	case 'signup':
		$login = Request::get('login');
		$password = Request::get('password');
		if ($login && $password)
		{
			$User = \Model\User::create($login, $password);
			if ($User)
			{
				if ($User->save())
				{
					$response['result'] = true;
					$response['user'] = $User;
				}
				else
				{
					$response['error'] = 'Cannot save data in database';
				}
			}
			else
			{
				$response['error'] = 'User with the same login already exists';
			}
		}
		else
		{
			$response['error'] = 'Required fields: login, password';
		}
		break;

	case 'login':
		$login = Request::get('login');
		$password = Request::get('password');
		$Token = \Model\User::login($login, $password);
		if ($Token)
		{
			$response['result'] = true;
			$response['token'] = $Token->id;
		}
		else
		{
			$response['error'] = 'Wrong credentials';
		}
		break;

	case 'auth':
		$token = Request::get('token');
		$User = \Model\User::auth($token);
		if ($User)
		{
			$response['result'] = true;
			$response['user'] = $User;
		}
		else
		{
			$response['error'] = 'Wrong token';
		}
		break;
}

Request::json($response);

exit;

$request = !empty($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);

if (count($request))
{
	// save result passed from application
	$Result = new \Model\Result();
	$Result->set($request);
	if ($Result->area_id && $Result->risk_id)
	{
		$Result->save();
	}

	echo ("uploading");
	echo json_encode($Result);
	exit;
}

$results = \Model\Result::model()->findList([], 'id asc');
?>
<table width="840" border="1">
	<thead>
		<tr>
			<td width="60">id</td>
			<td width="200">area</td>
			<td width="200">risk family</td>
			<td width="200">risk</td>
			<td width="60">prob.</td>
			<td width="60">impact</td>
			<td width="60">severity</td>
		</tr>
	</thead>
	<tbody>
<?php foreach ($results as $Result) : ?>
		<tr>
			<td><?php echo $Result->id; ?></td>
			<td><?php echo htmlspecialchars($Result->getArea()->name); ?></td>
			<td><?php echo htmlspecialchars($Result->getRisk()->getParent()->name); ?></td>
			<td><?php echo htmlspecialchars($Result->getRisk()->name); ?></td>
			<td><?php echo intval($Result->probability); ?></td>
			<td><?php echo intval($Result->impact); ?></td>
			<td><?php echo intval($Result->severity); ?></td>
		</tr>
<?php endforeach; ?>
	</tbody>
</table>