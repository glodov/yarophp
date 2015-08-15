<?

namespace Model\Schema\Table;

class Token extends \Core\Database\Schema\Table
{

	public function getName()
	{
		return 'tokens';
	}

	protected function getFields()
	{
		return [
			'id'          => ['char(40)', 'not null'],
			'object'      => ['char(40)', 'not null'],
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

	public $id;
	public $object;
	public $object_id;

	public function getTableName()
	{
		return 'tokens';
	}

	public function getPrimary()
	{
		return ['id'];
	}

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

	public static function create(\Core\Object $Object)
	{
		$Token = new self();
		$Token->object = get_class($Object);
		$Token->object_id = $Object->id();
		do
		{
			$Token->generate();
		} while ($Token->hasCopy());

		if ($Token->saveNew())
		{
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
	public static function auth($id, $class = null)
	{
		$Token = self::model()->findItem(['id = ' . $id]);
		if ($Token->id && (null === $class || $class == $Token->object))
		{
			if ($Token->expire_at >= Date::encode())
			{
				$Token->expire_at = Date::encode(time() + $Token->expire_time);
				$Token->save();
				return $Token->getObject();
			}
			else
			{
				$Token->drop();
			}
		}
		return null;
	}

}
