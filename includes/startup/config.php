<?

namespace Startup;

/**
 * Configuration startup class.
 *
 * @author Yarick.
 */
class Config
{

	public function __construct()
	{
		$time = \Helper\File::filemtime(\Application::dirConfig());
		if (false === ($files = \Application::cacheGet($this, $time)))
		{
			$time = false;
			$files = glob(\Application::dirConfig().'/*');
		}
		foreach ($files as $file)
		{
			if (preg_match('/\.php$/i', $file))
			{
				include($file);
			}
			else if (preg_match('/\.json/i', $file))
			{
				self::loadJSON($file);
			}
			else if (preg_match('/\.ini/i', $file))
			{
				self::loadINI($file);
			}
			else
			{
				\Application::log($this, 'Uknown filetype: '.$file);
			}
		}
		if (!$time)
		{
			\Application::cachePut($this, $files);
		}
	}

	/**
	 * Loads INI file into Config.
	 *
	 * @static
	 * @access private
	 * @param string $file The file path.
	 */
	private static function loadINI($file)
	{
		foreach (file($file) as $i => $line)
		{
			if (preg_match('/^[\s]*\#/', $line) || trim($line) === '')
			{
				continue;
			}
			$arr = explode('=', $line, 2);
			if (2 === count($arr))
			{
				$value = explode('#', $arr[1]);
				\Core\Config::set(trim($arr[0]), trim($value[0]));
			}
			else
			{
				\Application::log($this, 'Line #'.($i + 1).' in '.basename($file));
			}
		}
	}

	/**
	 * Loads JSON file into Config.
	 *
	 * @static
	 * @access private
	 * @param string $file The file path.
	 */
	private static function loadJSON($file)
	{
		\Core\Config::set(json_decode(file_get_contents($file), true));
	}

}

new Config();