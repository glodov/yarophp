<?

namespace Core\Database\Schema;

class Field
{
	const FIXED = [
		'BIT'                => 1,
		'TINYINT'            => 4,
		'TINYINT UNSIGNED'   => 3,
		'SMALLINT'           => 6,
		'SMALLINT UNSIGNED'  => 5,
		'MEDIUMINT'          => 9,
		'MEDIUMINT UNSIGNED' => 8,
		'INT'                => 11,
		'INT UNSIGNED'       => 10,
		'BIGINT'             => 20,
		'BIGINT UNSIGNED'    => 20,
		'DECIMAL'            => '10,0',
		'YEAR'               => 4
	];

	private $table;

	public
		$name,
		$type,
		$collation,
		$null,
		$key,
		$default,
		$auto_increment,
		$comment;

	public function __construct(Table $table, $name = null, $data = null)
	{
		$this->table = $table;
		if (isset($name['Field']))
		{
			$this->loadSql($name);
		}
		else if (is_string($name))
		{
			$this->loadModel($name, $data);
		}
	}

	public function getTable()
	{
		return $this->table;
	}

	private function loadSql($data)
	{
		$this->name           = $data['Field'];
		$this->type           = strtoupper($data['Type']);
		$this->collation      = $data['Collation'];
		$this->null           = strtoupper($data['Null']) == 'YES';
		$this->key            = $data['Key'];
		$this->default        = $data['Default'];
		$this->auto_increment = strtolower($data['Extra']) == 'auto_increment';
		$this->comment        = '' == $data['Comment'] ? null : $data['Comment'];
	}

	private function loadModel($name, $data)
	{
		$this->name = $name;
		if ('*id' === $data)
		{
			$this->type = 'INT';
			$this->null = false;
			$this->auto_increment = true;

			$this->table->addIndex(0, ['PRIMARY', 'BTREE', [$this->name]]);
			return;
		}
		foreach ($data as $i => $option)
		{
			if (0 == $i)
			{
				$this->type = strtoupper($option);
			}
			else if (preg_match('/^(not null|null)$/i', $option, $res))
			{
				$this->null = strtoupper($res[1]) == 'NULL';
			}
			else if (preg_match('/^default (.+)$/i', $option, $res))
			{
				$this->default = trim($res[1], "'\"");
			}
			else if (strtolower($option) == 'auto_increment')
			{
				$this->auto_increment = true;
			}
			else if (preg_match('/^comment (.+)$/i', $option, $res))
			{
				$this->comment = trim($res[1], "'\"");
			}
			else
			{
				$this->collation = $option;
			}
		}
	}

	public function decodeType()
	{
		$str = preg_replace('/(,\s+)/', ',', $this->type);
		$arr = preg_split('/\s+/', strtoupper($str));
		$type = trim($arr[0]);
		if (false !== ($index = array_search('UNSIGNED', $arr)))
		{
			$type .= ' UNSIGNED';
			unset($arr[$index]);
		}
		if (false === strpos($type, '('))
		{
			if (array_key_exists($type, self::FIXED))
			{
				$type = preg_replace('/^([\S]+)(\s*)/', '$1(' . self::FIXED[$type] . ')$2', $type);
			}
		}
		unset($arr[0]);
		array_unshift($arr, $type);
		return implode(' ', $arr);
	}

	public function isEmpty()
	{
		return null === $this->type;
	}

	public function sql()
	{
		$options = [];
		$options[] = $this->decodeType();
		if ($this->collation)
		{
			$options[] = $this->collation;
		}
		if (is_bool($this->null))
		{
			$options[] = $this->null ? 'NULL' : 'NOT NULL';
		}
		if ($this->auto_increment)
		{
			$options[] = 'auto_increment';
		}
		if (null !== $this->default)
		{
			$options[] = 'default "' . addslashes($this->default) . '"';
		}
		if (null !== $this->comment)
		{
			$options[] = 'comment "' . addslashes($this->comment) . '"';
		}
		return implode(' ', $options);
	}

	public function __toString()
	{
		return $this->name . ': ' . $this->sql();
	}

}
