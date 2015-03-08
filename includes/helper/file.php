<?

namespace Helper;

/**
 * The class File works with filesystem. It helps to detect files mimetype, 
 * extension, reads files in directory, etc.
 * 
 * @author Yarick
 * @version 0.2
 */
class File
{
	
	private static $uploadError = null;
	
	/**
	 * The function returns the extension of the filename.
	 * 
	 * @static
	 * @access public
	 * @param string $file The filename or path of the file.
	 * @param bool $lowerCase The flag of extension case. If TRUE 
	 * extensions will be represented in lower case.
	 * @return string The extension of the filename.
	 */
	public static function extension( $filename, $lowerCase = false )
	{
		$ext = substr( $filename, strrpos( $filename, '.' ) + 1 );
		return $lowerCase ? strtolower( $ext ) : $ext;
	}
	
	/**
	 * The function returns mimetype detected by file extension.
	 * 
	 * @static
	 * @access public
	 * @param string $filename The filename or path of the file.
	 * @return string The mimetype of the file.
	 */
	public static function mimetype( $filename )
	{
		$types = array(
			'afl'	=> 'video/animaflex',
			'aif'	=> 'audio/aiff',
			'aifc'	=> 'audio/aiff',
			'aiff'	=> 'audio/aiff',
			'art'	=> 'image/x-jg',
			'asf'	=> 'video/x-ms-asf',
			'asx'	=> 'video/x-ms-asf',
			'au'	=> 'audio/basic',
			'avi'	=> 'video/avi',
			'avs'	=> 'video/avs-video',
			'bm'	=> 'image/bmp',
			'bmp'	=> 'image/bmp',
			'dif'	=> 'video/x-dv',
			'dl'	=> 'video/dl',
			'doc'	=> 'application/msword',
			'docx'	=> 'application/msword',
			'dv'	=> 'video/x-dv',
			'dwg'	=> 'image/vnd.dwg',
			'dxf'	=> 'image/vnd.dwg',
			'fif'	=> 'image/fif',
			'fli'	=> 'video/fli',
			'flo'	=> 'image/florian',
			'flv'	=> 'video/flash',
			'fmf'	=> 'video/x-atomic3d-feature',
			'fpx'	=> 'image/vnd.fpx',
			'funk'	=> 'audio/make',
			'g3'	=> 'image/g3fax',
			'gif'	=> 'image/gif',
			'gl'	=> 'video/gl',
			'gsd'	=> 'audio/x-gsm',
			'gsm'	=> 'audio/x-gsm',
			'ico'	=> 'image/x-icon',
			'ief'	=> 'image/ief',
			'iefs'	=> 'image/ief',
			'isu'	=> 'video/x-isvideo',
			'it'	=> 'audio/it',
			'jam'	=> 'audio/x-jam',
			'jfif'	=> 'image/jpeg',
			'jfif-tbnl'	=> 'image/jpeg',
			'jpe'	=> 'image/jpeg',
			'jpeg'	=> 'image/jpeg',
			'jpg'	=> 'image/jpeg',
			'jps'	=> 'image/x-jps',
			'jut'	=> 'image/jutvision',
			'kar'	=> 'audio/midi',
			'la'	=> 'audio/nspaudio',
			'lam'	=> 'audio/x-liveaudio',
			'lma'	=> 'audio/nspaudio',
			'm1v'	=> 'video/mpeg',
			'm2a'	=> 'audio/mpeg',
			'm2v'	=> 'video/mpeg',
			'm3u'	=> 'audio/x-mpequrl',
			'mcf'	=> 'image/vasa',
			'mid'	=> 'audio/midi',
			'midi'	=> 'audio/midi',
			'mjf'	=> 'audio/x-vnd.audioexplosion.mjuicemediafile',
			'mjpg'	=> 'video/x-motion-jpeg',
			'mod'	=> 'audio/mod',
			'moov'	=> 'video/quicktime',
			'mov'	=> 'video/quicktime',
			'movie'	=> 'video/x-sgi-movie',
			'mp2'	=> 'audio/mpeg',
			'mp3'	=> 'audio/mpeg3',
			'mpa'	=> 'audio/mpeg',
			'mpe'	=> 'video/mpeg',
			'mpeg'	=> 'video/mpeg',
			'mpg'	=> 'audio/mpeg',
			'mpga'	=> 'audio/mpeg',
			'mv'	=> 'video/x-sgi-movie',
			'my'	=> 'audio/make',
			'nap'	=> 'image/naplps',
			'naplps'	=> 'image/naplps',
			'nif'	=> 'image/x-niff',
			'niff'	=> 'image/x-niff',
			'odt'	=> 'application/vnd.oasis.opendocument.text',
			'pbm'	=> 'image/x-portable-bitmap',
			'pct'	=> 'image/x-pict',
			'pcx'	=> 'image/x-pcx',
			'pdf'	=> 'application/pdf',
			'pfunk'	=> 'audio/make',
			'pgm'	=> 'image/x-portable-graymap',
			'pic'	=> 'image/pict',
			'pict'	=> 'image/pict',
			'pm'	=> 'image/x-xpixmap',
			'png'	=> 'image/png',
			'pnm'	=> 'image/x-portable-anymap',
			'ppm'	=> 'image/x-portable-pixmap',
			'qcp'	=> 'audio/vnd.qcelp',
			'qif'	=> 'image/x-quicktime',
			'qt'	=> 'video/quicktime',
			'qtc'	=> 'video/x-qtc',
			'qti'	=> 'image/x-quicktime',
			'qtif'	=> 'image/x-quicktime',
			'ra'	=> 'audio/x-pn-realaudio',
			'ram'	=> 'audio/x-pn-realaudio',
			'rar'	=> 'application/rar',
			'ras'	=> 'image/cmu-raster',
			'rast'	=> 'image/cmu-raster',
			'rf'	=> 'image/vnd.rn-realflash',
			'rgb'	=> 'image/x-rgb',
			'rm'	=> 'audio/x-pn-realaudio',
			'rmi'	=> 'audio/mid',
			'rmm'	=> 'audio/x-pn-realaudio',
			'rmp'	=> 'audio/x-pn-realaudio',
			'rp'	=> 'image/vnd.rn-realpix',
			'rpm'	=> 'audio/x-pn-realaudio-plugin',
			'rv'	=> 'video/vnd.rn-realvideo',
			's3m'	=> 'audio/s3m',
			'scm'	=> 'video/x-scm',
			'sid'	=> 'audio/x-psid',
			'snd'	=> 'audio/basic',
			'svf'	=> 'image/vnd.dwg',
			'tif'	=> 'image/tiff',
			'tiff'	=> 'image/tiff',
			'tsi'	=> 'audio/tsp-audio',
			'tsp'	=> 'audio/tsplayer',
			'turbot'	=> 'image/florian',
			'vdo'	=> 'video/vdo',
			'viv'	=> 'video/vivo',
			'vivo'	=> 'video/vivo',
			'voc'	=> 'audio/voc',
			'vos'	=> 'video/vosaic',
			'vox'	=> 'audio/voxware',
			'vqe'	=> 'audio/x-twinvq-plugin',
			'vqf'	=> 'audio/x-twinvq',
			'vql'	=> 'audio/x-twinvq-plugin',
			'wav'	=> 'audio/wav',
			'wbmp'	=> 'image/vnd.wap.wbmp',
			'xbm'	=> 'image/x-xbitmap',
			'xdr'	=> 'video/x-amt-demorun',
			'xif'	=> 'image/vnd.xiff',
			'xls'	=> 'application/msexcel',
			'xlsx'	=> 'application/msexcel',
			'xm'	=> 'audio/xm',
			'xpm'	=> 'image/x-xpixmap',
			'x-png'	=> 'image/png',
			'xsr'	=> 'video/x-amt-showrun',
			'xwd'	=> 'image/x-xwd',
			'zip'	=> 'application/zip',
		);
		$ext = self::extension( $filename, true );
		return isset( $types[ $ext ] ) ? $types[ $ext ] : 'application/unknown';
	}
	
	/**
	 * The function returns filesize in human format, like 1 392,44M
	 * 
	 * @static
	 * @access public
	 * @param int $value The filesize.
	 * @return string The filesize in human format.
	 */
	public static function size( $value )
	{
		$params = array( 1024, 'K' );
		if ( $value > 1024 * 1024 * 1024 )
		{
			$params = array( 1024 * 1024 * 1024, 'G' );
		}
		else if ( $value > 1024 * 1024 )
		{
			$params = array( 1024 * 1024, 'M' );
		}
		return number_format( $value / $params[0], 2, ',', ' ' ).$params[1];
	}
	
	/**
	 * The function restores path of file (create folder if it does not exist)
	 * with current permissions.
	 * 
	 * @static
	 * @access public
	 * @param string $path The path to the file.
	 * @param int $perm The permissions code in octal format.
	 */
	public static function restore( $path, $perm = 0777 )
	{
		if ( !is_dir( dirname( $path ) ) )
		{
			return mkdir( dirname( $path ), $perm, true );
		}
		return true;
	}
	
	/**
	 * The function execute command depends on server OS.
	 * 
	 * @static
	 * @access public
	 * @param string $cmd The command to execute.
	 * @return array The array with output lines;
	 */
	public static function exec( $cmd )
	{
		$result = array();
		if ( substr( PHP_OS, 0, 3 ) == 'WIN' )
		{
			$result = explode( "\n", shell_exec( $cmd ) );
		}
		else
		{
			exec( $cmd, $result );
		}
		return $result;
	}
	
	/**
	 * The function sends file to output.
	 * 
	 * @static
	 * @access public
	 * @param string $file The path to file.
	 * @param string $name The name of file which will be sent to headers, if 
	 * empty then basename of $file will be taken.
	 */
	public static function output( $file, $name = null )
	{
		if ( !file_exists( $file ) )
		{
			return false;
		}
		if ( !$name )
		{
			$name = basename( $file );
		}
	    header( 'Content-Description: File Transfer' );
	    header( 'Expires: 0' );
	    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	    header( 'Pragma: public' );
		header( 'Content-Type: '.self::mimetype( $file ) );
		header( 'Content-Disposition: attachment; filename="'.$name.'"' );
	    header( 'Content-Length: ' . filesize( $file ) );
		return readfile( $file );
	}
	
	/**
	 * The function converts multiple file array to single for uploading.
	 * 
	 * @static
	 * @access public
	 * @param array $file The posted file array.
	 * @param int $index The index key.
	 */
	public static function convertMultiple( array $file, $index )
	{
		return array(
			'name'		=> $file['name'][ $index ],
			'type'		=> $file['type'][ $index ],
			'tmp_name'	=> $file['tmp_name'][ $index ],
			'error'		=> $file['error'][ $index ],
			'size'		=> $file['size'][ $index ],
		);		
	}

	/**
	 * This function returns the time when the data blocks of a file/directory were 
	 * being written to, that is, the time when the content of the file/directory 
	 * was changed.
	 *
	 * @static
	 * @param string Path to file.
	 * @return integer The last time modification, FALSE on failure.
	 */
	public static function filemtime($file)
	{
		if (is_dir($file))
		{
			$max = 0;
			foreach (glob($file . '/*') as $file)
			{
				if (($time = self::filemtime($file)) > $max)
				{
					$max = $time;
				}
			}
			return $max;
		}
		return filemtime($file);
	}
	
}
