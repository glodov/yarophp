<?

include_once(__DIR__.'/core/autoload.php');

/**
 * The main application (dispatcher) class.
 * 
 * constants:
 * - DIR_CACHE
 * - DIR_CONFIG
 * - DIR_STARTUP
 *
 * @author Yarick.
 */
class Application
{

	private static $log = [];

	private function __construct($args)
	{
		foreach ($args as $module)
		{
			$file = self::fileStartup($module);
			self::log($this, $file);
			file_exists($file) && include($file);
		}
	}

	public static function run()
	{
		return new self(func_get_args());
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
		return \Core\Database::getInstance();
	}

	public static function log($source, $message = '')
	{
		self::$log[] = [$source, $message];
	}

	public static function getLog($divider = "\n")
	{
		$result = '';
		foreach (self::$log as $item)
		{
			$name = is_object($item[0]) ? get_class($item[0]) : $item[0];
			$result .= sprintf('%s: %s', $name, $item[1]) . $divider;
		}
		return $result;
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

}
