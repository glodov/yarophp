<?

namespace Controller;

abstract class Base extends \Core\Controller
{

	protected function redirect($link = '')
	{
		$url = rtrim(\Helper\URL::get($this), '/') . '/' . ltrim($link, '/');
		header('Location: ' . rtrim($url, '/'));
		exit;
	}

}
