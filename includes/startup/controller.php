<?

namespace Startup;

use \Core\Runtime as Runtime;

/**
 * Controller startup class.
 *
 * @author Yarick.
 */
class Controller
{

	public function __construct(\Application $App)
	{
		session_start();

		\Helper\Locale::set('en_US');

		$User = \Model\User::auth();
		if (!$User)
		{
			$User = \Model\User::model();
		}
		Runtime::set('USER', $User);

		$uri = empty($_SERVER['REQUEST_URI']) ? '/' : $_SERVER['REQUEST_URI'];
		$uri = preg_replace('/\.[\d\w]+$/', '', $uri);

		Runtime::set('REQUEST_URI', $uri);
		Runtime::set('HTTP_PROTOCOL', isset($_SERVER['HTTP_PROTOCOL']) ? $_SERVER['HTTP_PROTOCOL'] : 'http://');
		Runtime::set('HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');

		$class = null;
		$slug = $uri;
		foreach (\Core\Config::get('route', []) as $path => $class)
		{
			\Helper\URL::map($class, $path);
			if (0 === strpos(strtolower($uri), strtolower($path)) && in_array(substr($uri, strlen($path), 1), ['/', '?', '']))
			{
				$slug = ltrim(substr($uri, strlen($path)), '/');
				break;
			}
		}
		if (!$class)
		{
			$class = \Core\Config::get('default@route', '\\Controller\\Frontend');
		}

		\Helper\Console::log('Route: ' . $class);

		$response = \Core\Controller::executeController($class, $slug);
		$App->setResponse($response);
	}

}

new Controller($this);
