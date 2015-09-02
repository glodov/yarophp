<?

namespace Model\Schema\Table;

class Risk extends \Core\Database\Schema\Table
{
	public function getName()
	{
		return 'risks';
	}

	protected function getFields()
	{
		return [
			'id'          => '*id',
			'parent_id'   => ['int'],
			'name'        => ['varchar(150)']
		];
	}

	protected function getIndexes()
	{
		return [
			['parent_id']
		];
	}
}

namespace Model;

class Risk extends \Core\Object
{

	public $id;
	public $parent_id;
	public $name;

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
