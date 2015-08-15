<?


namespace Model\Schema\Table\Content;

class Webpage extends \Core\Database\Schema\Table
{

	public function getName()
	{
		return 'webpages';
	}

	protected function getFields()
	{
		return [
			'id'        => '*id',
			'title'     => ['varchar(64)', 'not null'],
			'seo'       => ['text'],
			'content'   => ['mediumtext'],

			'created_at'   => ['timestamp', 'default current_timestamp'],
			'published_at' => ['timestamp', 'null'],
			'modified_at'  => ['timestamp', 'null', 'on update current_timestamp'],
			'expire_at'    => ['timestamp', 'null']
		];
	}

	protected function getIndexes()
	{
		return [
			['title'],
			['published_at']
		];
	}

}

namespace Model\Content;

class Webpage extends \Core\ObjectI18n
{
	public
		$id,
		$title,
		$seo,
		$content;

	public function getObjectColumns()
	{
		return [
			'seo' => '\\Model\\Content\\SEO'
		];
	}

	public function getI18nColumns()
	{
		return ['title', 'seo', 'content'];
	}

	protected function getPrimary()
	{
		return ['id'];
	}

	protected function getTableName()
	{
		return 'webpages';
	}

}
