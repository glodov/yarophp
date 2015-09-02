<?

namespace Model\Schema\Table;

class Language extends \Core\Database\Schema\Table
{
	public function getName()
	{
		return 'languages';
	}

	protected function getFields()
	{
		return [
			'id'        => '*id',
			'locale'    => ['char(5)', 'not null'],
			'name'      => ['varchar(50)', 'not null'],
			'is_active' => ['tinyint', 'not null', 'default 0'],
			'position'  => ['int', 'not null']
		];
	}

	protected function getIndexes()
	{
		return [
			['unique', ['locale']],
			['is_active'],
			['position']
		];
	}
}

namespace Model;

class Language extends \Core\Object
{
	public
		$id,
		$locale,
		$name,
		$is_active,
		$position;

	public function saveNew()
	{
		$this->position = self::getLast($this, 'position') + 1;
		return parent::saveNew();
	}

}
