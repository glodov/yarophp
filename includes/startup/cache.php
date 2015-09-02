<?

namespace Startup;

class Cache
{

	public function __construct()
	{
		if (!is_dir(\Application::dirCache()))
		{
			mkdir(\Application::dirCache(), 0777, true);
		}
		if (!is_writable(\Application::dirCache()))
		{
			\Helper\Console::log('Cannot write into: ' . \Application::dirCache());
			\Application::halt(500);
		}
	}

}

new Cache();
