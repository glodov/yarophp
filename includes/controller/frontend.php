<?

namespace Controller;

use \Core\Runtime as Runtime;

class Frontend extends Base
{

	public function isAccess($method = null)
	{
		return true;
	}

	public function noAccess()
	{
	}

	public function noMethod()
	{
		return $this->theme('noMethod');
	}

	public function getWebpage()
	{
		$Object = Runtime::get('ROUTE_OBJECT');
		$Webpage = Runtime::get('ROUTE_WEBPAGE');
		if ($Webpage && $Webpage->id)
		{
			return $Webpage;
		}
		// if ($Object instanceof \Model\Content\Webpage)
		// {
		// 	return $Object;
		// }
		return new \Model\Content\Webpage();
	}

	public function getLanguage()
	{
		return Runtime::get('LANGUAGE');
	}

	protected function onLoad($method, $args)
	{
		\Helper\Console::log('Loaded: ' . $method . '(' . implode(', ', $args) . ')');
		// $this->attachCSS('app.css');
		// $this->attachScript('//js/script.js');
	}

	protected function getExtraData($method)
	{
		return [];
	}

	public function index()
	{
		return $this->theme('index');
		// return $this->getView()->render();
	}

}
