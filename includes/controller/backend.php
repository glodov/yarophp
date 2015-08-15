<?

namespace Controller;

class Backend extends Base
{

	public function isAccess($method = null)
	{
		return in_array(strtolower($method), ['login']);
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
		$this->attachCSS('backend.css');
		$this->attachScript('backend.js');
	}

	public function getUser()
	{
		return \Core\Runtime::get('USER');
	}

	public function index()
	{
		return $this->getView()->render();
	}

	public function login()
	{
		return $this->getView()->render();
	}

}
