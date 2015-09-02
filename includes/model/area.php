<?

namespace Model\Schema\Table;

class Area extends \Core\Database\Schema\Table
{
	public function getName()
	{
		return 'areas';
	}

	protected function getFields()
	{
		return [
			'id'        => '*id',
			'name'      => ['varchar(150)', 'not null']
		];
	}

	protected function getIndexes()
	{
		return [];
	}
}

namespace Model;

class Area extends \Core\Object
{

	public $id;
	public $name;

}
