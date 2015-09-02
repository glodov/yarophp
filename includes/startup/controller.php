<?

namespace Startup;

use \Core\Runtime as Runtime;
use \Model\Language as Language;

/**
 * Controller startup class.
 *
 * @author Yarick.
 */
class Controller
{
	private $class, $slug, $app;

	public function __construct(\Application $App)
	{
		session_start();
		$this->app = $App;

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

		if ($this->checkBackend($uri))
		{
			return $this->run();
		}
		$this->checkFrontend($uri);
		return $this->run(true);
	}

	private function checkBackend($uri)
	{
		$this->class = null;
		$this->slug = $uri;
		foreach (\Core\Config::get('route', []) as $path => $class)
		{
			if ('Controller\\Frontend' == $class)
			{
				continue;
			}
			\Helper\URL::map($class, $path);
			if (0 === strpos(strtolower($uri), strtolower($path)) && in_array(substr($uri, strlen($path), 1), ['/', '?', '']))
			{
				$this->slug = ltrim(substr($uri, strlen($path)), '/');
				$this->class = $class;
				return true;
			}
		}
		return false;
	}

	private function checkLanguage($uri)
	{
		$uri = preg_replace('/([&\#\?].*)$/', '', $uri);
		$uri = explode('/', trim($uri, '/'));
		$Language = Language::model();
		if (2 == strlen($uri[0]) || 5 == strlen($uri[0]))
		{
			$Language = Language::model()->findItem(['locale = ' . $uri[0]]);
			if ($Language->id)
			{
				array_shift($uri);
			}
		}
		if (!$Language->id)
		{
			$arr = Language::model()->findList(['is_active = 1'], 'position asc', 0, 1);
			foreach ($arr as $Language);
		}
		if (!$Language->id)
		{
			throw new \ErrorException("No enabled language found");
		}
		Runtime::set('LANGUAGE', $Language);
		\Helper\Locale::set($Language->locale);
		return '/' . implode('/', $uri);
	}

	private function checkFrontend($uri)
	{
		$this->class = null;
		$uri = $this->checkLanguage($uri);
		if ($Route = \Model\Route::detect(Runtime::get('LANGUAGE'), $uri))
		{
			Runtime::set('ROUTE_URL', $Route->url);
			$Object = $Route->getObject();
			Runtime::set('ROUTE_OBJECT', $Object);
			$this->slug = ltrim(substr($uri, strlen($Route->url)), '/');
			if ($Object instanceof \Model\Content\Webpage)
			{
				Runtime::set('ROUTE_WEBPAGE', $Object);
				$this->class = $Object->getController();
			}
		}
		else
		{
			$this->slug = ltrim($uri, '/');
		}
		if (!$this->class)
		{
			$Webpage = \Model\Content\Webpage::model()->findItem(['controller = NOTFOUND']);
			Runtime::set('ROUTE_WEBPAGE', $Webpage);
			$this->class = $Webpage->getController();
		}
		return true;
	}

	private function run($exact = false)
	{
		$response = \Core\Controller::executeController($this->class, $this->slug, $exact);
		\Helper\Console::log('Route: ' . $this->class);
		$this->app->setResponse($response);
	}

}

new Controller($this);
