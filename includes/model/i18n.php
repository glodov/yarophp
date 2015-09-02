<?

namespace Model\Schema\Table;

class I18n extends \Core\Database\Schema\Table
{

	public function getName()
	{
		return 'i18n';
	}

	protected function getFields()
	{
		return [
			'id'        => '*id',
			'locale'    => ['char(5)', 'not null'],
			'object'    => ['varchar(50)', 'not null'],
			'object_id' => ['int', 'not null'],
			'field'     => ['varchar(50)', 'not null'],
			'value'     => ['mediumtext', 'not null']
		];
	}

	protected function getIndexes()
	{
		return [
			['unique', ['locale', 'object', 'object_id', 'field']],
			[['locale', 'object', 'object_id']],
			['field']
		];
	}

}

namespace Model;

class I18n extends \Core\Object
{
	private static $locales;

	public
		$id,
		$locale,
		$object,
		$object_id,
		$field,
		$value;

	public static function getLocales()
	{
		if (!isset(self::$locales))
		{
			self::$locales = Language::model()->findList([], 'position asc');
		}
		return self::$locales;
	}

	public static function getTranslation(\Core\ObjectI18n $Object)
	{
		if (!$Object->id)
		{
			return null;
		}
		$params = [
			'object = ' . get_class($Object),
			'object_id = ' . $Object->id
		];
		$result = [];
		$cols = $Object->getI18nColumns();
		foreach (self::model()->findList($params, 'locale asc') as $Item)
		{
			if (!isset($result[$Item->locale]))
			{
				$result[$Item->locale] = [];
			}
			$result[$Item->locale][$Item->field] = $Item->value;
			foreach ($cols as $col)
			{
				if (!isset($result[$Item->locale][$col]))
				{
					$result[$Item->locale][$col] = null;
				}
			}
		}
		return $result;
	}

	public static function attach(\Core\ObjectI18n $Object, $locale, $field, $value = '')
	{
		$Item = new self();
		$Item->locale    = $locale;
		$Item->object    = get_class($Object);
		$Item->object_id = $Object->id;
		$Item->field     = $field;
		$Item->value     = $value;
		$Copy = $Item->findCopy(['locale', 'object', 'object_id', 'field']);
		if ($Copy->id)
		{
			$Item = $Copy;
			$Item->value = $value;
		}
		return $Item->save();
	}

	public static function detach(\Core\ObjectI18n $Object)
	{
		$params = [
			'object = ' . get_class($Object),
			'object_id = ' . $Object->id
		];
		return self::model()->dropList($params);
	}

}
