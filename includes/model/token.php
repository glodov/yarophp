<?

namespace Model;

class Token extends \Core\Object
{

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

}
