<?

namespace Controller;

<<<<<<< HEAD
use \Core\Runtime as Runtime;

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
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
<<<<<<< HEAD
		return $this->theme('noMethod');
=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	}

	public function getWebpage()
	{
<<<<<<< HEAD
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
=======
		return new \Model\Content\Webpage();
	}

	protected function onLoad($method, $args)
	{
		\Helper\Console::log('Loaded: ' . $method . '(' . implode(', ', $args) . ')');
		$this->attachCSS('app.css');
		$this->attachScript('script.js');
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	}

	public function index()
	{
<<<<<<< HEAD
		return $this->theme('index');
		// return $this->getView()->render();
=======
		return $this->getView()->render();
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	}

}
