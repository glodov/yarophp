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
<<<<<<< HEAD
			'id'         => '*id',
			'title'      => ['varchar(64)', 'not null'],
			'url'        => ['varchar(100)', 'not null'],
			'controller' => ['enum("BOOKING","CATALOG","PRODUCT","CART","NOTFOUND")'],
			'seo'        => ['text'],
			'content'    => ['mediumtext'],
			'is_active'  => ['tinyint', 'default 0'],
=======
			'id'        => '*id',
			'title'     => ['varchar(64)', 'not null'],
			'seo'       => ['text'],
			'content'   => ['mediumtext'],
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

			'created_at'   => ['timestamp', 'default current_timestamp'],
			'published_at' => ['timestamp', 'null'],
			'modified_at'  => ['timestamp', 'null', 'on update current_timestamp'],
			'expire_at'    => ['timestamp', 'null']
		];
	}

	protected function getIndexes()
	{
		return [
<<<<<<< HEAD
			['url'],
=======
			['title'],
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
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
<<<<<<< HEAD
		$url,
		$controller,
		$seo,
		$content,
		$created_at,
		$published_at,
		$modified_at,
		$expire_at,
		$is_active;
=======
		$seo,
		$content;
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

	public function getObjectColumns()
	{
		return [
<<<<<<< HEAD
			'seo'     => 'Model\\Content\\SEO',
=======
			'seo' => '\\Model\\Content\\SEO'
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		];
	}

	public function getI18nColumns()
	{
<<<<<<< HEAD
		return ['title', 'url', 'seo', 'content', 'is_active'];
	}

	public function getController()
	{
		$arr = [
			'BOOKING'  => 'Controller\\Frontend\\Booking',
			'CATALOG'  => 'Controller\\Frontend\\Catalog',
			'PRODUCT'  => 'Controller\\Frontend\\Product',
			'CART'     => 'Controller\\Frontend\\Cart',
			'NOTFOUND' => 'Controller\\Frontend\\Notfound'
		];
		return isset($arr[$this->controller]) ? $arr[$this->controller] : \Core\Config::get('default@route', 'Controller\\Frontend');
	}

	public function save()
	{
		if (parent::save())
		{
			$this->saveRoute();
			return true;
		}
		return false;
	}

	private function saveRoute()
	{
		$tr = $this->getTranslations(true);
		if (!$this->hasChanged(['url']) && (null == $tr || !count($tr)))
		{
			return false;
		}
		\Model\Route::attach($this, null, $this->url, $this->is_active);
		foreach ($tr as $locale => $data)
		{
			\Model\Route::attach($this, $locale, $data['url'], $data['is_active']);
		}
		return true;
	}

	public function drop()
	{
		if (parent::drop())
		{
			\Model\Route::detach($this);
			return true;
		}
		return false;
=======
		return ['title', 'seo', 'content'];
	}

	protected function getPrimary()
	{
		return ['id'];
	}

	protected function getTableName()
	{
		return 'webpages';
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	}

}
