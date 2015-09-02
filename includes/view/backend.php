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

<<<<<<< HEAD
	protected function vBody()
	{
		$child = strtolower(ltrim(str_replace('View\\Backend', '', get_class($this)), '\\'));
		$file = [];
		if ($child)
		{
			$file[] = $child;
		}
		$file[] = $this->getMethod();
		$file = implode(DIRECTORY_SEPARATOR, $file);
		return $this->includeLayout($file);
	}

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
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
		if (!array_key_exists('Webpage', $result))
		{
			$result['Webpage'] = $this->getController()->getWebpage();
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
