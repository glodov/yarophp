<?

namespace Core;

/**
 * The Object I18n class.
 * Using to add multilanguage functionality to models.
 *
 * @abstract
 * @author Yarick.
 * @version 0.2
 */
abstract class ObjectI18n extends Object
{
<<<<<<< HEAD
	private $i18n;
=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

	/**
	 * Returns array of multilanguage columns.
	 *
	 * @abstract
	 * @access public
	 * @return array The multilanguage columns.
	 */
	abstract public function getI18nColumns();

<<<<<<< HEAD
	public function getLocales()
	{
		$result = [];
		foreach (\Model\I18n::getLocales() as $Locale)
		{
			$result[$Locale->locale] = $Locale->name;
		}
		return $result;
	}

	public function getTranslations($onlyLocal = false)
	{
		if (null === $this->i18n && !$onlyLocal)
		{
			$this->i18n = \Model\I18n::getTranslation($this);
		}
		return $this->i18n;
	}

	public function toArray($only = null)
	{
		$result = parent::toArray($only);
		if (is_array($only) && !in_array('i18n', $only))
		{
			return $result;
		}
		$i18nFields = $this->getI18nColumns();
		$tr = $this->getTranslations();
		$oc = $this->getObjectColumns();
		$result['i18n'] = [];
		foreach ($this->getLocales() as $locale => $name)
		{
			$result['i18n'][$locale] = [];
			foreach ($i18nFields as $field)
			{
				$className = isset($oc[$field]) ? $oc[$field] : null;
				$value = null;
				if (isset($tr[$locale], $tr[$locale][$field]))
				{
					$value = $className && is_string($tr[$locale][$field]) ? unserialize($tr[$locale][$field]) : $tr[$locale][$field];
				}
				else
				{
					$value = $className ? new $className() : null;
				}
				if (is_object($value) && method_exists($value, 'toArray'))
				{
					$value = $value->toArray();
				}
				$result['i18n'][$locale][$field] = $value;
			}
		}
		return $result;
	}

	public function i18n($locale, $onlyLocal = false)
	{
		$i18n = $this->getTranslations($onlyLocal);
		return isset($i18n[$locale]) ? $i18n[$locale] : null;
	}

	public function setPost(array $data = [], $only = null)
	{
		parent::setPost($data, $only);
		$oc = $this->getObjectColumns();
		if (isset($data['i18n']))
		{
			$this->i18n = [];
			foreach ($data['i18n'] as $locale => $arr)
			{
				$this->i18n[$locale] = [];
				foreach ($arr as $field => $value)
				{
					$className = isset($oc[$field]) ? $oc[$field] : null;
					$this->i18n[$locale][$field] = $className ? self::fromArray($value, $className) : $value;
				}
			}
		}
	}

	public function save()
	{
		if (parent::save())
		{
			$this->saveI18n();
			return true;
		}
		return false;
	}

	private function saveI18n()
	{
		if (!is_array($this->i18n))
		{
			return false;
		}
		foreach ($this->i18n as $locale => $arr)
		{
			foreach ($arr as $field => $value)
			{
				\Model\I18n::attach($this, $locale, $field, $value);
			}
		}
		return true;
	}

	public function drop()
	{
		if (parent::drop())
		{
			$this->dropI18n();
			return true;
		}
		return false;
	}

	private function dropI18n()
	{
		return \Model\I18n::detach($this);
	}

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
}
