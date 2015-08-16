<?

namespace Model\Schema\Table;

class User extends \Core\Database\Schema\Table
{

	public function getName()
	{
		return 'users';
	}

	protected function getFields()
	{
		return [
			'id' => '*id',
			'login' => ['varchar(100)', 'not null'],
			'password' => ['varchar(50)', 'not null'],
			'level' => ['tinyint unsigned', 'not null', 'default 0']
		];
	}

	protected function getIndexes()
	{
		return [
			['unique', 'login'],
			['level']
		];
	}

}

namespace Model;

class User extends \Core\Object
{

	const LEVEL_ADMIN = 128;     // 1000 0000
	const LEVEL_MANAGER = 64;    // 0100 0000
	const LEVEL_MODERATOR = 32;  // 0010 0000
	const LEVEL_CUSTOMER = 16;   // 0001 0000
	// ..
	const LEVEL_VISITOR = 1;     // 0000 0001

	public $id;
	public $login;
	public $level;
	protected $password;

	public function getTableName()
	{
		return 'users';
	}

	public function getPrimary()
	{
		return ['id'];
	}

	public static function login($login, $password, $remember = false)
	{
		$User = self::model()->findItem(['login = ' . $login]);
		if (!$User->id)
		{
			return false;
		}
		$hashed_password = $User->password;
		if (hash_equals($hashed_password, crypt($password, $hashed_password)))
		{
			return Token::create($User, $remember);
		}
		return false;
	}

	public static function logout($token = null)
	{
		return Token::remove($token, 'Model\\User');
	}

	public static function auth($token = null)
	{
		return Token::auth($token, 'Model\\User');
	}

	private static function salt()
	{
		return sha1(time() . rand(0, 9999999) . microtime(true));
	}

	public static function create($login, $password, $level = self::LEVEL_CUSTOMER)
	{
		$User = new self();
		$User->login = $login;
		$User->password = crypt($password, self::salt());
		$User->level = $level;
		if ($User->hasCopy('login'))
		{
			return false;
		}
		return $User;
	}

}
