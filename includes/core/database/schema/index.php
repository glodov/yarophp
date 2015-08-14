<?

namespace Core\Database\Schema;

class Index
{

	public
		$name,
		$primary = false,
		$unique = false,
		$fulltext = false,
		$columns = [],
		$collation,
		$type,
		$comment;

	public function __construct(Table $table, $name = null, $data = null)
	{
		$this->table = $table;
		if (is_array($name))
		{
			$data = $name;
		}
		if (!is_array($data))
		{
			return;
		}
		foreach ($data as $row)
		{
			if (is_array($row) && isset($row['Key_name']))
			{
				$this->loadSql($data);
			}
			else
			{
				$this->name = $name;
				$this->loadModel($data);
			}
			break;
		}
	}

	public function getTable()
	{
		return $this->table;
	}

	private function loadSql($data)
	{
		$columns = $sort = [];
		foreach ($data as $i => $row)
		{
			if (!$i)
			{
				$this->name = $row['Key_name'];
				if ('PRIMARY' == $this->name)
				{
					$this->primary = true;
				}
				$this->unique = $row['Non_unique'] == '0';
				$this->collation = $row['Collation'];
				if ('FULLTEXT' == $row['Index_type'])
				{
					$this->fulltext = true;
				}
				else
				{
					$this->type = $row['Index_type'];
				}
				$this->comment = $row['Comment'];
			}
			$columns[] = $row['Column_name'];
			$sort[] = $row['Seq_in_index'];
		}
		array_multisort($sort, SORT_ASC, SORT_NUMERIC, $columns);
		$this->columns = $columns;
	}

	private function loadModel($data)
	{
		$this->columns = [];
		foreach ($data as $option)
		{
			if (is_array($option))
			{
				$this->columns = $option;
			}
			else if ('PRIMARY' == strtoupper($option))
			{
				$this->primary = true;
			}
			else if ('UNIQUE' == strtoupper($option))
			{
				$this->unique = true;
			}
			else if ('FULLTEXT' == strtoupper($option))
			{
				$this->fulltext = true;
			}
			else if (preg_match('/^(BTREE|HASH)$/i', $option, $res))
			{
				$this->type = strtoupper($res[1]);
			}
			else if (preg_match('/^comment (.+)$/i', $option, $res))
			{
				$this->comment = trim($res[1], "'\"");
			}
			else
			{
				$this->columns[] = $option;
			}
		}
		if ($this->primary)
		{
			$this->unique = true;
			$this->name = 'PRIMARY';
		}
		if (is_numeric($this->name))
		{
			$this->name = 'idx_' . $this->name;
		}
	}

	private function getColumns()
	{
		$result = [];
		foreach ($this->columns as $column)
		{
			$result[] = \Core\Database::getInstance()->map($column);
		}
		return implode(',', $result);
	}

	public function sql()
	{
		$options = [];
		if ($this->primary)
		{
			$options[] = 'PRIMARY KEY';
		}
		else if ($this->unique)
		{
			$options[] = 'UNIQUE';
		}
		else if ($this->fulltext)
		{
			$options[] = 'FULLTEXT';
		}
		else
		{
			$options[] = 'INDEX';
		}
		if (!$this->fulltext)
		{
			$options[] = 'USING ' . ('HASH' == $this->type ? 'HASH' : 'BTREE');
		}
		$options[] = '(' . $this->getColumns() . ')';
		return implode(' ', $options);
	}

	public function __toString()
	{
		$options = [];
		$options[] = '(' . implode(', ', $this->columns) . ')';
		if ($this->unique)
		{
			$options[] = 'unique';
		}
		$options[] = $this->type;
		if ($this->collation)
		{
			$options[] = $this->collation;
		}
		if ($this->comment)
		{
			$options[] = 'comment "' . addslashes($this->comment) . '"';
		}
		return ($this->primary ? 'PRI' : $this->name) . ': ' . implode(' ', $options);
	}

}
