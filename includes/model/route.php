<?

namespace Model\Schema\Table;

class Route extends \Core\Database\Schema\Table
{

	public function getName()
	{
		return 'routes';
	}

	protected function getFields()
	{
		return [
			'id'          => '*id',
			'locale'      => ['char(5)', 'not null'],
			'url'         => ['varchar(100)', 'not null'],
			'object'      => ['varchar(50)', 'not null'],
			'object_id'   => ['int', 'not null'],
			'is_active'   => ['tinyint', 'not null', 'default 1']
		];
	}

	protected function getIndexes()
	{
		return [
			['unique', ['locale', 'url']],
			[['object', 'object_id']],
			['is_active']
		];
	}

}

namespace Model;

use \Helper\Date as Date;

class Route extends \Core\Object
{

	public
		$id,
		$locale,
		$url,
		$object,
		$object_id,
		$is_active;

	public function getObject()
	{
		$name = $this->object;
		$Object = new $name();
		return $Object->findItem(['id = ' . $this->object_id]);
	}

	private function detectByUri(Language $Language, $uri)
	{
		$uri = explode('/', trim($uri, '/'));
		$urls = [];
		for ($i = 0; $i < count($uri); $i++)
		{
			$urls[] = $this->db()->quote('/' . implode('/', array_slice($uri, 0, count($uri) - $i)));
		}
		$urls[] = $this->db()->quote('/');
		$stack = [];
		$params = [];
		$params[] = '* url in (' . implode(', ', $urls) . ')';
		foreach ($this->findList($params, 'url desc') as $Route)
		{
			if ($Route->locale == $Language->locale)
			{
				return $Route;
			}
			else if ($Route->locale == "null")
			{
				$stack[] = $Route;
			}
		}
		if (count($stack))
		{
			return $stack[0];
		}
		return null;
	}

	public static function attach(\Core\Object $Object, $locale, $url, $is_active = true)
	{
		$Item = new self();
		$Item->locale    = $locale ? $locale : 'null';
		$Item->url       = $url;
		$Item->object    = get_class($Object);
		$Item->object_id = $Object->id;
		$Item->is_active = intval($is_active);
		$Copy = $Item->findCopy(['locale', 'url', 'object', 'object_id']);
		if ($Copy->id)
		{
			$Item = $Copy;
			$Item->is_active = intval($is_active);
		}
		return $Item->save();
	}

	public static function detach(\Core\Object $Object)
	{
		$params = [
			'object = ' . get_class($Object),
			'object_id = ' . $Object->id
		];
		return self::model()->dropList($params);
	}

	public static function detect(Language $Language, $uri)
	{
		return self::model()->detectByUri($Language, $uri);
	}

}
