<?

namespace Startup;

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

		$User = \Model\User::auth(\Helper\Request::get('X-Auth-Token'));
		if (!$User)
		{
			$User = \Model\User::model();
		}
		\Core\Runtime::set('USER', $User);

		$uri = empty($_SERVER['REQUEST_URI']) ? '/' : $_SERVER['REQUEST_URI'];
		$uri = preg_replace('/\.[\d\w]+$/', '', $uri);

		$class = null;
		$slug = $uri;
		foreach (\Core\Config::get('route', []) as $path => $class)
		{
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
		\Core\Runtime::set('VIEW_DIR', \Application::dirRoot() . DIRECTORY_SEPARATOR . 'views');

		$response = \Core\Controller::executeController($class, $slug);
		$App->setResponse($response);
	}

}

new Controller($this);
