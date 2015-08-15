<?

include_once(__DIR__.'/core/autoload.php');

use \Helper\Console as Console;

/**
 * The main application (dispatcher) class.
 *
 * constants:
 * - DIR_CACHE
 * - DIR_CONFIG
 * - DIR_LOGS
 * - DIR_STARTUP
 *
 * @author Yarick.
 */
class Application
{
	private $response, $charset = 'utf8';

	private function __construct($args)
	{
		foreach ($args as $module)
		{
			$file = self::fileStartup($module);
			Console::log('Startup: ' . self::basename($file));
			file_exists($file) && include($file);
		}
	}

	public function setResponse($response)
	{
		$this->response = $response;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function charset($charset = null)
	{
		if (null !== $charset)
		{
			$this->charset = $charset;
		}
		return $this->charset;
	}

	public static function run()
	{
		Console::start();
		self::checkDirectories();
		return new self(func_get_args());
	}

	public static function basename($file)
	{
		$root = dirname(__DIR__);
		if (0 === strpos($file, $root))
		{
			return str_replace($root, '', $file);
		}
		return $file;
	}

	private static function checkDirectories()
	{
		foreach ([self::dirCache(), self::dirLogs()] as $dir)
		{
			if (!file_exists($dir))
			{
				mkdir($dir, 0777, true);
			}
			if (!is_dir($dir) || !is_writable($dir))
			{
				exit("Unable to write into $dir");
			}
		}
	}

	public static function halt($errorCode = 404)
	{
		if (200 !== $errorCode)
		{
			printf(self::getLog());
		}
		exit;
	}

	/**
	 * Returns database object.
	 *
	 * @static
	 * @access public
	 * @return \Core\Database The database object.
	 */
	public static function db()
	{
		return \Core\Database::getInstance([
			'name'       => \Core\Config::get('name@db'),
			'user'       => \Core\Config::get('user@db'),
			'password'   => \Core\Config::get('pass@db'),
			'host'       => \Core\Config::get('host@db'),
			'persistent' => \Core\Config::get('pers@db'),
			'charset'    => \Core\Config::get('char@db'),
			'collation'  => \Core\Config::get('collation@db')
			]);
	}

	/**
	 * Cache data for object or returns cached data.
	 *
	 * @static
	 * @access public
	 * @param mixed $target The object.
	 * @param mixed $data The data to cache.
	 * @return mixed TRUE on success cache save, FALSE on failure.
	 */
	public static function cachePut($target, $data = null)
	{
		$file = self::fileCache($target);
		if (!is_dir(dirname($file)))
		{
			mkdir(dirname($file), 0777, true);
		}
		$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		return file_put_contents($file, $json) > 0;
	}

	/**
	 * Returns cached data or FALSE.
	 *
	 * @static
	 * @param mixed $target The object.
	 * @param integer $timestamp The age of actual object.
	 * Cache must be younger than timestamp.
	 * @return mixed The cached data or FALSE on failure.
	 */
	public static function cacheGet($target, $timestamp)
	{
		$file = self::fileCache($target);
		if (file_exists($file) && \Helper\File::filemtime($file) >= $timestamp)
		{
			return json_decode(file_get_contents($file), true);
		}
		return false;
	}

	/**
	 * Returns cache directory.
	 *
	 * @static
	 * @access public
	 * @return string The directory.
	 */
	public static function dirCache()
	{
		return defined('DIR_CACHE') ? DIR_CACHE : dirname(__DIR__).'/cache';
	}

	/**
	 * Returns cache file path.
	 *
	 * @static
	 * @access public
	 * @param mixed $target The object.
	 * @return string The file path.
	 */
	public static function fileCache($target)
	{
		$filename = is_object($target)
			? preg_replace('/[\\\_]+/', '/', get_class($target)) : $target;
		return self::dirCache().'/'.$filename.'.json';
	}

	/**
	 * Returns root project directory.
	 *
	 * @static
	 * @access public
	 * @return string The directory.
	 */
	public static function dirRoot()
	{
		return defined('DIR_ROOT') ? DIR_ROOT : dirname(__DIR__);
	}

	/**
	 * Returns config directory.
	 *
	 * @static
	 * @access public
	 * @return string The directory.
	 */
	public static function dirConfig()
	{
		return defined('DIR_CONFIG') ? DIR_CONFIG : dirname(__DIR__).'/config';
	}

	/**
	 * Returns startup directory.
	 *
	 * @static
	 * @access public
	 * @return string The directory.
	 */
	public static function dirStartup()
	{
		return defined('DIR_STARTUP') ? DIR_STARTUP : __DIR__.'/startup';
	}

	/**
	 * Returns logs directory.
	 *
	 * @static
	 * @access public
	 * @return string The directory.
	 */
	public static function dirLogs()
	{
		return defined('DIR_LOGS') ? DIR_LOGS : dirname(__DIR__) . '/logs';
	}

	/**
	 * Returns startup file path.
	 *
	 * @static
	 * @access public
	 * @return string The file path.
	 */
	public static function fileStartup($filename)
	{
		return self::dirStartup().'/'.$filename.'.php';
	}

	/**
	 * Returns array of models names.
	 *
	 * @static
	 * @access public
	 * @return array The models names.
	 */
	public static function getModels()
	{
		$result = [];
		$path = __DIR__ . '/model/';
		$Iterator = new RecursiveDirectoryIterator($path);
		$objects = new RecursiveIteratorIterator($Iterator, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($objects as $name => $object)
		{
			if (strtolower(substr($name, -4) != '.php'))
			{
				continue;
			}
			$name = strtolower(str_replace($path, '', substr($name, 0, strlen($name) - 4)));
			$name = str_replace(DIRECTORY_SEPARATOR, '\\', $name);
			$name = '\\Model\\' . ucwords($name, '\\');
			if (class_exists($name))
			{
				$result[]= $name;
			}
		}
		return $result;
	}

}
