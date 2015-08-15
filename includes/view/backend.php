<?

namespace View;

class Backend extends Base
{
	protected function extension()
	{
		return '.php';
	}

	protected function getLayoutDir()
	{
		return parent::getLayoutDir() . DIRECTORY_SEPARATOR . 'backend';
	}

	public function get($key = null)
	{
		$result = parent::get($key);
		if (null !== $key)
		{
			return $result;
		}
		if (!array_key_exists('User', $result))
		{
			$result['User'] = $this->getController()->getUser();
		}
		return $result;
	}

	public function index()
	{
		return $this->includeLayout('index');
	}

	public function login()
	{
		return $this->includeLayout('login');
	}
}
