<?

namespace Model\Schema\Table\I18n;

class Something extends \Core\Database\Schema\Table
{

	public function getName()
	{
		return 'tests';
	}

	protected function getFields()
	{
		return [
			'a' => ['int', 'not null'],
			'b' => ['int unsigned zerofill', 'not null'],
			'c' => ['varchar(10)', 'not null', 'comment just comment'],
			'd' => ['datetime', 'not null']
		];
	}

	protected function getIndexes()
	{
		return [
			['PRIMARY', ['a', 'b']],
			['fulltext', ['c']],
			[['c', 'd']]
		];
	}

}

namespace Model\I18n;

class Something extends \Core\Object
{

	public function getTableName()
	{
		return 'users';
	}

	public function getPrimary()
	{
		return ['id'];
	}

}
