<?

<<<<<<< HEAD
namespace Controller
{
	abstract class Base extends \Core\Controller
	{
		private static $urls = [];

		protected function redirect($link = '')
		{
			$url = rtrim(\Helper\URL::get($this), '/') . '/' . ltrim($link, '/');
			header('Location: ' . rtrim($url, '/'));
			exit;
		}

		protected function isAjax()
		{
			return (bool)\Helper\Request::get('ajax');
		}

		public static function getUrl($target, $locale = null)
		{
			if (null === $locale)
			{
				$locale = \Helper\Locale::get();
			}
			$hash = $locale . '-' . $target;
			if (!isset(self::$urls[$hash]))
			{
				self::$urls[$hash] = '/';
				if (is_string($target))
				{
					$Webpage = \Model\Content\Webpage::model();
					if (in_array($target, $Webpage->getEnum('controller')))
					{
						$Webpage = $Webpage->findItem(['controller = ' . $target]);
						if ($Webpage->id)
						{
							$i18n = $Webpage->getTranslations();
							if (isset($i18n[$locale]['url']) && intval($i18n[$locale]['is_active']) > 0)
							{
								self::$urls[$hash] = $i18n[$locale]['url'];
							}
						}
					}
				}
			}
			return self::$urls[$hash];
		}

	}
}

namespace
{
	function _l($target, $locale = null)
	{
		return \Controller\Base::getUrl($target, $locale);
	}
=======
namespace Controller;

abstract class Base extends \Core\Controller
{

	protected function redirect($link = '')
	{
		$url = rtrim(\Helper\URL::get($this), '/') . '/' . ltrim($link, '/');
		header('Location: ' . rtrim($url, '/'));
		exit;
	}

>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
}
