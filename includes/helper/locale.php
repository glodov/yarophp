<?

namespace Helper
{

	use \Core\Runtime as Runtime;

	/**
	 * The localization class.
	 * Stores loaded translations.
	 *
	 * @author Yarick.
	 * @version 1.0
	 */
	class Locale
	{

		private static $data = [], $file = [], $locale = 'en_US';

		public static $TEST_MODE = true;

		/**
		 * The function clears the loaded transations.
		 *
		 * @static
		 * @access public
		 */
		public static function clear()
		{
			self::$data = [];
			self::$file = [];
		}

		/**
		 * The function returns full locale file path.
		 *
		 * @static
		 * @access protected
		 * @return string The locale file path.
		 */
		private static function getFilePath( $file )
		{
			return Runtime::get('LOCALE_DIR').'/'.self::get().'/'.$file;
		}

		/**
		 * Returns available locales.
		 *
		 * @static
		 * @access public
		 * @return array The locales.
		 */
		public static function getLocales()
		{
			$result = [];
			$dir = Runtime::get('LOCALE_DIR') . '/';
			foreach (glob($dir . '*') as $file)
			{
				if (is_dir($file))
				{
					$result []= substr($file, strlen($dir));
				}
			}
			return $result;
		}

		/**
		 * The function loads data to language container from array or file.
		 *
		 * @static
		 * @access public
		 * @param mixed $data The array of translation or file to translation file.
		 * You also use many parameters to load few files.
		 * @param boolean $force If TRUE rewrites data even if file already loaded.
		 * @example
		 * Locale::add( 'Main', 'Tooltips', 'Frontend' );
		 */
		public static function load( $data, $force = false )
		{
			if ( is_array( $data ) )
			{
				self::$data = array_merge( self::$data, $data );
			}
			else
			{
				if ( func_num_args() > 1 && !is_bool($force) )
				{
					for ( $i = 0; $i < func_num_args(); $i++ )
					{
						self::load( func_get_arg( $i ) );
					}
				}
				else
				{
					if (isset(self::$file[$data]) && !$force)
					{
						return false;
					}
					$filename = preg_match("/\\.(php|ini)$/i", $data) ? $data : ($data . '.ini');
					$path = self::getFilePath( $filename );
					if ( file_exists( $path ) )
					{
						$res = \Helper\File::readConfig($path);
						self::$file[$data] = $res;
						self::$data = array_merge( self::$data, $res );
					}
					else
					{
						// file not found
						self::$file[$data] = array();
					}
				}
			}
			return true;
		}

		/**
		 * The function sets current locale.
		 *
		 * @static
		 * @access public
		 * @param string $locale The current locale.
		 */
		public static function set( $locale )
		{
			if ( !$locale )
			{
				$locale = 'en_US';
			}
			return self::$locale = $locale;
		}

		/**
		 * The function returns locale value by key from language container.
		 *
		 * @static
		 * @access public
		 * @param string $key The language key.
		 * @param string $file The file namespace.
		 * @return mixed Locale value or array of values.
		 */
		public static function get( $key = null, $file = null )
		{
			if ( $key === null )
			{
				return self::$locale;
			}
			if ($file && !isset(self::$file[$file]))
			{
				self::load($file);
			}
			$data = $file ? self::$file[$file] : self::$data;
			if ($key === true)
			{
				return $data;
			}
			if ( isset( $data[ $key ] ) )
			{
				return $data[ $key ];
			}
			$testMode = self::$TEST_MODE;
			return $testMode ? (self::$locale.'['.$key.']') : $key;
		}

		/**
		 * The function returns TRUE if translation for key exists and FALSE otherwise.
		 *
		 * @static
		 * @access public
		 * @return bool TRUE if translation for key exists and FALSE otherwise.
		 */
		public static function has( $key )
		{
			return isset( self::$data[ $key ] );
		}

		/**
		 * The function translates input array.
		 *
		 * @static
		 * @access public
		 * @param array $array The input array.
		 * @return array The translated array.
		 */
		public static function translate( $data, $file = null )
		{
			if (is_array($data))
			{
				$result = array();
				foreach ( $data as $key => $value )
				{
					$result[ $key ] = self::get( $value, $file );
				}
				return $result;
			}
			else
			{
				$result = $data;
				$data = self::get(true, $file);
				$sort = array();
				foreach ($data as $key => $value)
				{
					$sort[] = strlen($key);
				}
				array_multisort($sort, SORT_DESC, SORT_NUMERIC, $data);
				foreach ($data as $key => $value)
				{
					$result = str_replace($key, $value, $result);
				}
				return $result;
			}
		}

		/**
		 * The function returns enabled locales.
		 *
		 * @static
		 * @access public
		 * @return array The locales.
		 */
		public static function getEnabled()
		{
			$arr = array();
			$enabled = Config::get('l10n', '');
			if ( !$enabled )
			{
				return array();
			}
			$enabled = explode( ',', $enabled );
			foreach ( ISO_Language::getInstance()->getAssocData() as $code => $data )
			{
				if ( in_array( $code, $enabled ) )
				{
					$arr[ $code ] = $data;
				}
			}
			return $arr;
		}

		/**
		 * The function returns language name by its code.
		 *
		 * @static
		 * @access public
		 * @param string $code The language code.
		 * @return string The name.
		 */
		public static function getName( $code )
		{
			return ISO_Language::getInstance()->get( $code );
		}

		/**
		 * The function returns prefix of current localization.
		 *
		 * @static
		 * @access public
		 * @return string The prefix.
		 */
		public static function getPrefix()
		{
			return substr(self::get(), 0, 2);
		}

	}

}


namespace
{

	/**
	 * The function returns translation for current key.
	 * Can be used extra arguments for patters {arg1}, {arg2}, {argN}.
	 *
	 * @param string $key The translation key.
	 * @return string The translated string.
	 */
	function _t( $key )
	{
		$str = \Helper\Locale::get( $key );
		if ( func_num_args() > 1 )
		{
			$args = func_get_args();
			for ( $i = 1; $i < count( $args ); $i++ )
			{
				$repl['{'.$i.'}'] = $args[ $i ];
			}
			$str = strtr( $str, $repl );
		}
		return $str;
	}

}
