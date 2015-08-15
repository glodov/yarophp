<?

namespace Controller;

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
	}

	public function getWebpage()
	{
		return new \Model\Content\Webpage();
	}

	protected function onLoad($method, $args)
	{
		\Helper\Console::log('Loaded: ' . $method . '(' . implode(', ', $args) . ')');
		$this->attachCSS('app.css');
		$this->attachScript('script.js');
	}

	public function index()
	{
		return $this->getView()->render();
	}

}
