<?

namespace Startup;

class Database
{

	public function __construct()
	{
		\Application::db();
	}

}

new Database();