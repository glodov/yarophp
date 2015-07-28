<?

namespace Model;

class Risk extends \Core\Object
{

	public $id;
	public $parent_id;
	public $name;

	public function getTableName() { return 'risks'; }

	public function getPrimary() { return ['id']; }

	public function hasParent()
	{
		return $this->parent_id > 0;
	}

	public function getParent()
	{
		if (!$this->hasParent())
		{
			return null;
		}
		return self::model()->findItem(['id = '.$this->parent_id]);
	}

}
