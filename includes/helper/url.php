<?

namespace Helper
{
	use \Core\Runtime as Runtime;

	/**
	 * The static URL class for build links.
	 *
	 * @author Yarick.
	 * @version 0.3
	 */
	class URL
	{

		private static $absolute = false, $map = [];

		/**
		 * The function sets format of URL - absolute or relative.
		 *
		 * @static
		 * @access public
		 * @param bool $bool If TRUE all URL will be returned with absolute path (including host),
		 * if NULL just returns current value.
		 */
		public static function absolute( $bool = null )
		{
			if ( $bool !== null )
			{
				self::$absolute = (bool)$bool;
			}
			return self::$absolute;
		}

		/**
		 * The function returns correct absolute or relative URL.
		 *
		 * @static
		 * @access public
		 * @param string $link The link.
		 * @return string The URL.
		 */
		public static function abs( $link )
		{
			if ( preg_match( '/^(http|https|ssl|ftp):\/\//', $link ) )
			{
				return $link;
			}
			if ( !self::$absolute )
			{
				return '/'.ltrim( $link, '/' );
			}
			return Runtime::get('HTTP_PROTOCOL').Runtime::get('HTTP_HOST').'/'.ltrim( $link, '/' );
		}

		/**
		 * The function returns link for current object.
		 *
		 * @static
		 * @access public
		 * @param mixed $Object The object.
		 * @param string $tag The tag.
		 * @param bool $restoreGet If TRUE returns link with GET parameters.
		 * @return string The URL.
		 */
		public static function get( \Core\Controller $Controller, $restoreGet = false )
		{
			$url = '';
			$p = get_class($Controller);
			while ($p)
			{
				if (array_key_exists($p, self::$map))
				{
					$url = rtrim(self::$map[$p], '/') . str_replace('\\', '/', strtolower(str_replace($p, '', get_class($Controller))));
					break;
				}
				$obj = new $p();
				$p = get_parent_class($obj);
				if ('Controller\\Base' == $p)
				{
					break;
				}
			}
			if (!$url)
			{
				$url = isset(self::$map['\\Controller\\Frontend']) ? self::$map['\\Controller\\Frontend'] : '/';
			}
			if ( $restoreGet && count( $_GET ) )
			{
				$url .= strpos( $url, '?' ) === false ? '?' : '&';
				$url .= http_build_query( $_GET );
			}
			return self::abs($url);
		}

		/**
		 * The function returns TRUE if controller object is current URL.
		 *
		 * @static
		 * @access public
		 * @param Controller\Base $Ctrl The Controller.
		 * @return bool TRUE on success, FALSE on failure.
		 */
		public static function on(\Controller\Base $Ctrl)
		{
			return Runtime::get('REQUEST_URI') == self::get($Ctrl);
		}

		/**
		 * The function returns GET query.
		 *
		 * @static
		 * @access public
		 * @param array The data.
		 * @return string The query.
		 */
		public static function buildGet( array $data = array() )
		{
			$data = array_merge( $_GET, $data );
			return http_build_query( $data );
		}

		public static function map($controller, $link = null)
		{
			if (is_array($controller))
			{
				foreach ($controller as $name => $link)
				{
					self::map($name, $link);
				}
				return true;
			}
			if (is_object($controller))
			{
				$controller = get_class($controller);
			}
			self::$map[$controller] = $link;
		}
	}

}

namespace
{
	function _url(\Core\Controller $Controller, $restorGet = false)
	{
		return \Helper\URL::get($Controller, $restorGet);
	}
}
