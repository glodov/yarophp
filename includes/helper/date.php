<?

namespace Helper;

/**
 * The Date helper class.
 * 
 * @author Yarick.
 * @version 0.1
 */
class Date
{

	private $timestamp;
	private $format;

	private static $defaultFormat = 'd.m.Y';

	/**
	 * Date class constructor.
	 *
	 * @access public
	 * @param mixed $timestamp The date timestamp or date.
	 * @param string $format The default date format.
	 */
	public function __construct($timestamp, $format = null)
	{
		$this->setDate($timestamp);
		if (null !== $format)
		{
			$this->setFormat($format);
		}
	}
	
	/**
	 * Sets the timestamp for current date object.
	 *
	 * @access public
	 * @param mixed $timestamp The date timestamp or date.
	 */
	public function setDate($timestamp)
	{
		$this->timestamp = is_numeric($timestamp) ? $timestamp : self::decode($timestamp);
	}

	/**
	 * Sets the format for current date object.
	 *
	 * @access public
	 * @param string $format The date format.
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * Returns current date format.
	 *
	 * @access public
	 * @return string The date format.
	 */
	public function getFormat()
	{
		return $this->format ? $this->format : self::$defaultFormat;
	}

	/**
	 * Returns formatted date.
	 * Synonym of Date::encode()
	 *
	 * @see Date::encode()
	 */
	public function __toString()
	{
		return self::encode($this->timestamp, $this->getFormat());
	}

	/**
	 * Sets default date format.
	 *
	 * @static
	 * @access public
	 * @param string $format The format.
	 */
	public static function setDefaultFormat($format)
	{
		self::$defaultFormat = $format;
	}

	/**
	 * Extended strtotime function.
	 *
	 * @static
	 * @access public
	 * @param string $string The input string as a date.
	 * @param integer $now The timestamp to base on.
	 * @return integer The timestamp.
	 */
	public static function strtotime( $string, $now = null )
	{
		if ( preg_match( '/^(\d{1,2})\.(\d{1,2})\.(\d{1,2})$/', $string, $res ) ) // dd.mm.yy
		{
			return strtotime( sprintf( '%02d.%02d.%04d', $res[1], $res[2], $res[3] + ( $res[3] > 70 ? 1990 : 2000 ) ) );
		}
		if ( preg_match( '/^(\d{1,2})\.(\d{1,2})\.(\d{1,2}) (\d{2}):(\d{2})$/', $string, $res ) ) // dd.mm.yy hh:ii
		{
			return strtotime( sprintf( '%02d.%02d.%04d %02d:%02d', $res[1], $res[2], $res[3] + ( $res[3] > 70 ? 1990 : 2000 ), $res[4], $res[5] ) );
		}
		else if ( preg_match( '/^(\d{4})(\d{2})(\d{2})$/', $string, $res ) ) // yyyymmdd
		{
			return strtotime( sprintf( '%02d.%02d.%04d', $res[3], $res[2], $res[1] ) );
		}
		else if ( preg_match( '/^(\d{4})-(\d{2})$/', $string, $res ) ) // yyyy-mm
		{
			return strtotime( sprintf( '%02d.%02d.%04d', 1, $res[2], $res[1] ) );
		}
		else if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/', $string, $res ) ) // yyyy-mm-dd HH:ii
		{
			return strtotime( sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $res[1], $res[2], $res[3], $res[4], $res[5], 0 ) );
		}
		return strtotime( $string, $now );
	}

	/**
	 * Synonym of Date::strtotime() function.
	 *
	 * @see Date::strtotime()
	 */
	public static function decode($string, $now = null)
	{
		return self::strtotime($string, $now);
	}
	
	/**
	 * Returns formatted date.
	 *
	 * @static
	 * @access public
	 * @param mixed $timestamp The timestamp or date.
	 * @param string $format The date format to show.
	 * @return string The date.
	 */
	public static function encode($timestamp = null, $format = 'Y-m-d H:i:s')
	{
		if ($timestamp === null)
		{
			$timestamp = time();
		}
		if (!is_numeric($timestamp))
		{
			$timestamp = self::strtotime($timestamp);
		}
		return date($format, $timestamp);
	}
	
	/**
	 * Synonym of Date::encode()
	 *
	 * @see Date::encode()
	 */
	public static function show($timestamp = null, $format = 'd.m.y')
	{
		return self::encode($timestamp, $format);
	}

}
