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
		$uri = empty($_SERVER['REQUEST_URI']) ? '/' : $_SERVER['REQUEST_URI'];
		
		$arr = explode('/', preg_replace('/\.aspx$/', '', $uri));
		array_shift($arr);
		
		\Core\Controller::executeController('\\Controller\\Frontend', $arr);
	}

}

new Controller($this);