<?

namespace Core;

/**
 * The Autoload class for loading classes by names.
 * 
 * @version 0.1
 */
class Autoload
{
	/**
	 * The function loads class file by its name if file exists.
	 * 
	 * @static
	 * @access public
	 * @param string $class The class name.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public static function load( $class )
	{
		if ( ( $file = self::exist( $class ) ) )
		{
			include_once( $file );
			return true;
		}
		return false;
	}
	
	/**
	 * The function returns TRUE if class exists, otherwise FALSE.
	 * 
	 * @static
	 * @access public
	 * @param string $class The class name.
	 * @return bool TRUE if class exists, otherwise FALSE.
	 */
	public static function exist( $class )
	{
		$class = strtolower($class);
		$arr = preg_split('/[\\\_]+/', $class, 2);

		$dirs = [dirname(__DIR__)];
		foreach( $dirs as $dir )
		{
			$file = $dir.'/'.preg_replace('/[\\\_]+/', '/', $class).'.php';
			if ( file_exists( $file ) )
			{
				return $file;
			}
		}
		return false;
	}
	
}

spl_autoload_register('\Core\Autoload::load');
