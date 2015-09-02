<?

namespace View;

class Frontend extends Base
{
	protected function extension()
	{
		return '.php';
	}

	protected function getLayoutDir()
	{
		return parent::getLayoutDir() . DIRECTORY_SEPARATOR . 'frontend';
	}

	public function get($key = null)
	{
		$result = parent::get($key);
		if (null !== $key)
		{
			return $result;
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
}
