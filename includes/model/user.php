<?

namespace Model;

class User extends \Core\Object
{

	public $id;
	public $login;
	protected $password;

	public function getTableName() { return 'users'; }

	public function getPrimary() { return ['id']; }

	protected function getAutoIncrementField()
	{
		return 'id';
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
		$Token = Token::model()->findItem(['id = ' . $token]);
		if ($Token->id)
		{
			return $Token->getObject();
		}
		return false;
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
