<?

namespace Helpers\Image;

class Gmagick extends Image
{

	protected $img;

	/**
	 * @see \Helpers\Image::__construct()
	 */
	public function __construct($file = null)
	{
		$this->img = new \Gmagick();
		if ($file)
		{
			$this->load($file);
		}
	}

	/**
	 * @see \Helpers\Image::load()
	 */
	public function load($file)
	{
		$this->img->ReadImage($file);
	}

	/**
	 * @see \Helpers\Image::save()
	 */
	public function save($file = null)
	{
		if (preg_match('/\.(\w{2,4})$/', $file, $res))
		{
			$this->img->SetImageFormat(strtolower($res[1]));
		}
		$this->img->WriteImage($file);
	}

	/**
	 * @see \Helpers\Image::resize()
	 */
	public function resize($width, $height, $adaptive = false)
	{
		if (!$width || !$height)
		{
			return false;
		}
		$filter = \Gmagick::FILTER_CATROM;
		$blur = 1;
		if ($adaptive)
		{
			if (self::Fit === $this->getGravity())
			{
				$this->img->resizeImage($width, $height, $filter, $blur, true);
			}
			else
			{
				$geo = $this->getAdaptiveGeometry($width, $height);
				$this->img->resizeImage($geo['width'], $geo['height'], $filter, $blur);
				$this->img->cropImage($width, $height, $geo['left'], $geo['top']);
			}
		}
		else
		{
			$this->img->resizeImage($width, $height, $filter, $blur);
		}
		return true;
	}

	/**
	 * @see \Helpers\Image::getGeometry()
	 */
	public function getGeometry()
	{
		return [
			'width' => $this->getWidth(),
			'height' => $this->getHeight(),
		];
	}

	/**
	 * @see \Helpers\Image::getWidth()
	 */
	public function getWidth()
	{
		return $this->img->GetImageWidth();
	}

	/**
	 * @see \Helpers\Image::getHeight()
	 */
	public function getHeight()
	{
		return $this->img->GetImageHeight();
	}

}
