<?

namespace Core\Database\Schema;

abstract class Table
{

	/**
	 * Returns array of table fields.
	 *
	 * @abstract
	 * @access protected
	 * @return array The fields.
	 */
	abstract protected function getFields();

	/**
	 * Returns array of table indexes.
	 *
	 * @abstract
	 * @access protected
	 * @return array The indexes.
	 */
	abstract protected function getIndexes();

	/**
	 * Returns table name.
	 *
	 * @abstract
	 * @access public
	 * @return string The table name.
	 */
	abstract public function getName();

	protected $fields, $indexes;

	public function setup($in_model = false)
	{
		if ($in_model)
		{
			$this->getModelStructure();
		}
		else
		{
			$this->getDatabaseStructure();
		}
		return $this;
	}

	protected function getDatabaseStructure()
	{
		$table = self::db()->map($this->getName());
		$this->fields = $this->indexes = [];

		$columns = $indexes = $data = [];
		$arr = self::db()->query('SHOW FULL COLUMNS FROM '.$table);
		foreach ($arr as $row)
		{
			$this->addField($row);
		}
		$arr = self::db()->query('SHOW INDEXES FROM '.$table);
		foreach ($arr as $row)
		{
			if (!isset($data[$row['Key_name']]))
			{
				$data[$row['Key_name']] = [];
			}
			$data[$row['Key_name']][] = $row;
		}
		foreach ($data as $name => $arr)
		{
			$this->addIndex($arr);
		}
	}

	protected function getModelStructure()
	{
		foreach ($this->getFields() as $name => $options)
		{
			$this->addField($name, $options);
		}
		foreach ($this->getIndexes() as $name => $options)
		{
			$this->addIndex($name, $options);
		}
	}

	public function addField($name, $data = null)
	{
		$this->fields[] = new Field($this, $name, $data);
	}

	public function addIndex($name, $data = null)
	{
		$this->indexes[] = new Index($this, $name, $data);
	}

	public function getColumns($indexes = false)
	{
		return $indexes ? $this->indexes : $this->fields;
	}

	public function isEmpty()
	{
		return count($this->fields) == 0 && count($this->indexes) == 0;
	}

	public function __toString()
	{
		$string = [get_class($this) . ':'];
		foreach ($this->fields as $Field)
		{
			$string[] = "\t[F]" . $Field;
		}
		foreach ($this->indexes as $Index)
		{
			$string[] = "\t[I]" . $Index->name . ': ' . $Index->sql();
		}
		return implode("\n", $string);
	}

	protected static function db()
	{
		return \Core\Database::getInstance();
	}

}
