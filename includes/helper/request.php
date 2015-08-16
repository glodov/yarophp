<?

namespace Helper;

/**
 * The Request helper class.
 *
 * @author Yarick.
 * @version 0.1
 */
class Request
{

	private static $cache = array();

	/**
	 * Outputs data in JSON format with correct headers.
	 *
	 * @static
	 * @access public
	 * @param mixed $data The data to output.
	 * @param boolean $end If TRUE stops application after output.
	 */
	public static function json($data, $end = true)
	{
		header('Content-Type: application/json;charset=utf-8');
		echo json_encode($data);
		if ($end)
		{
			exit;
		}
	}

	/**
	 * The function returns value by its key from target global array.
	 *
	 * @static
	 * @access public
	 * @param string $key The value name.
	 * @param mixed $default The default value.
	 * @param string $target The array names separated by comma from
	 * 	which variable will be given.
	 * @return mixed The value.
	 */
	public static function get($key, $default = null, $target = 'GET,POST,ANGULAR')
	{
		$array = self::getData($target);
		if ( $key === null )
		{
			return $array;
		}
		return isset( $array[ $key ] ) ? $array[ $key ] : $default;
	}

	/**
	 * Returns mixed array from target.
	 *
	 * @static
	 * @access private
	 * @param string $target The array names separated by comma from
	 * 	which mixed result will be generated.
	 */
	private static function getData($target)
	{
		if (isset(self::$cache[$target]))
		{
			return self::$cache[$target];
		}
		$array = array();
		foreach ( explode( ',', $target ) as $word )
		{
			switch ( strtoupper( $word ) )
			{
				case 'SERVER':
					$array = array_merge( $array, $_SERVER );
					break;

				case 'COOKIE':
					$array = array_merge( $array, $_COOKIE );
					break;

				case 'GET':
					$array = array_merge( $array, $_GET );
					break;

				case 'POST':
					$array = array_merge( $array, $_POST );
					break;

				case 'SESSION':
					$array = array_merge( $array, $_SESSION );
					break;

				case 'ANGULAR':
					$data = file_get_contents("php://input");
					if ($data)
					{
						$data = json_decode($data, true);
						if (is_array($data))
						{
							$array = array_merge( $array, $data );
						}
					}
					break;
			}
		}
		self::$cache[$target] = $array;
		return $array;
	}

	/**
	 * The function sets global array value by its ket and target name.
	 *
	 * @static
	 * @access public
	 * @param string $target The target array name such as COOKIE,
	 * SERVER, GET, POST.
	 * @param string $key The value $key.
	 * @param mixed $value The value.
	 */
	public static function set( $target, $key, $value )
	{
		self::$cache = [];
		switch ( strtoupper( $target ) )
		{
			case 'GET':
				$_GET[ $key ] = $value;
				break;

			case 'POST':
				$_POST[ $key ] = $value;
				break;

			case 'SERVER':
				$_SERVER[ $key ] = $value;
				break;

			case 'COOKIE':
				$_COOKIE[ $key ] = $value;
				$args = func_get_args();
				$time = isset( $args[3] ) ? time() + $args[3] : null;
				$path = isset( $arg[4] ) ? $arg[4] : '/';
				$domain = isset( $arg[5] ) ? $arg[5] : null;
				setcookie( $key, $value, $time, $path, $domain );
				break;
		}
	}
}
