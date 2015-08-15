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
			'level' => ['tinyint', 'not null', 'default 0']
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

	const LEVEL_ADMIN = 127;
	const LEVEL_MANAGER = 63;
	const LEVEL_MODERATOR = 31;
	const LEVEL_CUSTOMER = 15;
	// ..
	const LEVEL_VISITOR = 0;

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

	public static function login($login, $password)
	{
		$User = self::model()->findItem(['login = ' . $login]);
		if (!$User->id)
		{
			return false;
		}
		$hashed_password = $User->password;
		if (hash_equals($hashed_password, crypt($password, $hashed_password)))
		{
			return Token::create($User);
		}
		return false;
	}

	public static function auth($token)
	{
		return Token::auth($token, '\\Model\\User');
	}

	private static function salt()
	{
		return sha1(time() . rand(0, 9999999) . microtime(true));
	}

	public static function create($login, $password)
	{
		$User = new self();
		$User->login = $login;
		$User->password = crypt($password, self::salt());
		if ($User->hasCopy('login'))
		{
			return false;
		}
		return $User;
	}

}
