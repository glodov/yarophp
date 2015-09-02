<?

namespace Startup;

/**
 * Configuration startup class.
 *
 * @author Yarick.
 */
class Config
{

	public function __construct(\Application $App)
	{
		$time = \Helper\File::filemtime($App::dirConfig());
		if (false === ($files = $App::cacheGet($this, $time)))
		{
			$time = false;
			$files = glob($App::dirConfig().'/*');
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
				\Helper\Console::log('Uknown filetype: '.$file);
			}
		}
		if (!$time)
		{
			$App::cachePut($this, $files);
<<<<<<< HEAD
		}
		$App->charset(\Core\Config::get('charset@db', 'utf8'));
		\Core\Runtime::set('CHARSET', $App->charset());

		foreach (\Core\Config::get('plugins', []) as $dir => $namespace)
		{
			\Core\Autoload::add($App::dirPlugins() . DIRECTORY_SEPARATOR . $dir, $namespace);
=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		}
		$App->charset(\Core\Config::get('charset@db', 'utf8'));
		\Core\Runtime::set('CHARSET', $App->charset());
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
				\Helper\Console::log('Line #'.($i + 1).' in '.basename($file));
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

new Config($this);
