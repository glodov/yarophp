<?

namespace Model\Schema\Table;

class Token extends \Core\Database\Schema\Table
{
	const EXPIRE_TIME = 3600;
	const EXPIRE_FOREVER = 31536000; // 365 days

	public function getName()
	{
		return 'tokens';
	}

	protected function getFields()
	{
		return [
			'id'          => ['char(40)', 'not null'],
			'object'      => ['varchar(50)', 'not null'],
			'object_id'   => ['int', 'not null'],
			'expire_at'   => ['timestamp'],
			'expire_time' => ['int', 'not null', 'default "' . \Model\Token::EXPIRE_TIME . '"']
		];
	}

	protected function getIndexes()
	{
		return [
			['PRIMARY', 'id']
		];
	}

}

namespace Model;

use \Helper\Date as Date;

class Token extends \Core\Object
{
	const EXPIRE_TIME = 3600;
	const EXPIRE_FOREVER = 31536000; // 365 days

	public
		$id,
		$object,
		$object_id,
		$expire_at,
		$expire_time;

	public function generate()
	{
		$this->id = sha1($this->object . $this->object_id . time() . rand(0, 9999999) . microtime(true));
	}

	public function getObject()
	{
		$name = $this->object;
		$Object = new $name();
		return $Object->findItem(['id = ' . $this->object_id]);
	}

	public static function create(\Core\Object $Object, $remember = false)
	{
		$Token = new self();
		$Token->object = get_class($Object);
		$Token->object_id = $Object->id();
		do
		{
			$Token->generate();
		} while ($Token->hasCopy());

		$Token->expire_time = $remember ? self::EXPIRE_FOREVER : self::EXPIRE_TIME;
		$Token->expire_at = Date::encode(time() + $Token->expire_time);

		if ($Token->saveNew())
		{
			\Helper\Request::set('COOKIE', 'X-Auth-Token', $Token->id, $Token->expire_time, '/');
			return $Token;
		}
		return false;
	}

	/**
	 * Authorises by token id. Returns object attached to current token.
	 *
	 * @static
	 * @access public
	 * @param string $id The token id.
	 * @param string $class The class name attached to token.
	 * @return \Model Model attached to token on success, NULL on failure.
	 */
	public static function auth($id = null, $class = null)
	{
		if (null === $id)
		{
			$id = \Helper\Request::get('X-Auth-Token', null, 'HEADERS,ANGULAR,COOKIE');
		}
		$Token = self::model()->findItem(['id = ' . $id]);
		if ($Token->id && (null === $class || $class == $Token->object))
		{
			if ($Token->expire_at >= Date::encode())
			{
				$Token->expire_at = Date::encode(time() + $Token->expire_time);
				$Token->save();
				\Helper\Request::set('COOKIE', 'X-Auth-Token', $Token->id, $Token->expire_time, '/');
				\Helper\Console::log('Auth OK for token: ' . $id);
				return $Token->getObject();
			}
			else
			{
				\Helper\Console::log('Token expired');
				$Token->drop();
			}
		}
		else
		{
			\Helper\Console::log('Auth by id [' . $id . '] FAILED');
		}
		return null;
	}

	public static function remove($id = null, $class = null)
	{
		if (null === $id)
		{
			$id = \Helper\Request::get('X-Auth-Token', null, 'HEADERS,ANGULAR,COOKIE');
		}
		$Token = self::model()->findItem(['id = ' . $id]);
		if ($Token->id && (null === $class || $class == $Token->object))
		{
			$Token->drop();
			\Helper\Request::set('COOKIE', 'X-Auth-Token', $Token->id, $Token->expire_time, '/');
			return true;
		}
		return false;
	}

}
