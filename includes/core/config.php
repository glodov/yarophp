<?

namespace Core;

/**
 * The Config class.
 * 
 * @author Yarick.
 * @version 0.1
 */
class Config
{
	
	private static $container = [];

	/**
	 * The function returns config value by its name.
	 * 
	 * @static
	 * @access public
	 * @param string $name The config value name.
	 * @param mixed $default The default returned value.
	 * @return mixed The config value.
	 */
	public static function get( $name, $default = null )
	{
		return isset( self::$container[ $name ] ) ? self::$container[ $name ] : $default;
	}
	
	/**
	 * The function sets config value by its name.
	 * The name can be associated array.
	 * 
	 * @static
	 * @access public
	 * @param mixed $name The config value name or array of values.
	 * @param mixed $value The config value.
	 */
	public static function set( $name, $value = null )
	{
		if ( is_array( $name ) )
		{
			foreach ( $name as $key => $value )
			{
				self::set( $key, $value );
			}
		}
		else
		{
			self::$container[ $name ] = $value;
		}
	}

	/**
	 * The function loads config values from file.
	 * File can be php which contains array or ini like simple config file.
	 * 
	 * @static
	 * @access public
	 * @param string $file The filepath.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public static function load( $file )
	{

		switch ( substr( $file, -4 ) )
		{
			case '.php':
				self::set( include( $file ) );
				break;
			
			case '.ini':
				foreach ( file($file) as $line )
				{
					if ( substr( $line, 0, 1 ) == '#' )
					{
						continue;
					}
					$arr = explode( '=', $line, 2 );
					if ( count( $arr ) != 2 )
					{
						continue;
					}
					self::set( trim( $arr[0] ), trim( $arr[1] ) );
				}
				break;
		}
	}
	
	/**
	 * The function loads config values from Object.
	 * 
	 * @static
	 * @access public
	 * @param object $Object The object.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public static function loadObject( Object $Object )
	{
		foreach ( $Object->findList() as $Item )
		{
			self::set( $Item->Name, @unserialize( $Item->Value ) );
		}
		return true;
	}

	/**
	 * The function returns data values into string separated by divider
	 *
	 * @static
	 * @access private
	 * @param array $data The data values.
	 * @param integer $level The depth level.
	 * @param string $divider The string divider.
	 * @param string $tab The tab value.
	 * @return string The config values.
	 */
	private static function showItem($data, $level, $divider = "\n", $tab = "\n")
	{
		$result = '';
		foreach ($data as $key => $value)
		{
			$result .= str_repeat("\t", $level) . $key . ' = ';
			if (is_scalar($value))
			{
				$result .= $value . $divider;
			}
			else
			{
				$result .= self::showItem($value, $divider, $level + 1);
			}
		}
		return $result;
	}
	
	/**
	 * The function returns config values to show.
	 * 
	 * @static
	 * @access public
	 * @param string $divider The string divider.
	 * @param string $tab The tab value.
	 * @return string The config values.
	 */
	public static function show($divider = "\n", $tab = "\t")
	{
		return self::showItem(self::$container, 0, $divider, $tab);
	}

}
