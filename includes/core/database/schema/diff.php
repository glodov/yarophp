<?

namespace Core\Database\Schema;

class Diff
{

	public static function compare(Table $model, Table $db)
	{
		$drop = $create = $change = [];
		if ($db->isEmpty() && !$model->isEmpty())
		{
			// create new table
			$create[] = $model;
		}
		else if ($model->isEmpty() && !$db->isEmpty())
		{
			// drop existent table
			$drop[] = $db;
		}
		else
		{
			// alter existent table
			$source = $target = [];
			foreach ($model->getColumns() as $Field)
			{
				$source[$Field->name] = $Field;
			}
			foreach ($db->getColumns() as $Field)
			{
				$target[$Field->name] = $Field;
			}
			foreach ($target as $name => $Field)
			{
				if (!isset($source[$name]))
				{
					// drop column
					$drop[] = $Field;
				}
			}
			foreach ($source as $name => $Field)
			{
				if (isset($target[$name]))
				{
					// change column
					$Field = self::changeField($Field, $target[$name]);
					if ($source[$name]->sql() != $target[$name]->sql())
					{
						$change[] = $source[$name];
					}
				}
				else
				{
					// create column
					$create[] = $Field;
				}
			}
			$source = $target = [];
			foreach ($model->getColumns(true) as $Index)
			{
				$source[] = $Index;
			}
			foreach ($db->getColumns(true) as $Index)
			{
				$target[] = $Index;
			}
			foreach ($target as $Index)
			{
				$found = false;
				foreach ($source as $Field)
				{
					if ($Field->sql() == $Index->sql())
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					// drop index
					$drop[] = $Index;
				}
			}
			foreach ($source as $Index)
			{
				$found = false;
				foreach ($target as $Field)
				{
					if ($Field->sql() == $Index->sql())
					{
						$found = true;
						break;
					}
				}
				if (!$found)
				{
					// create index
					$create[] = $Index;
				}
			}
		}
		return [
			'create' => $create,
			'change' => $change,
			'remove' => $drop,
		];
	}

	private static function changeField(Field $to, Field $from)
	{
		$Field = new Field($to->getTable());
		foreach ($to as $key => $value)
		{
			if ($from->$key !== $value)
			{
				$Field->$key = $value;
				$Field->forceChange();
			}
		}
		$Field->name = $to->name;
		$Field->type = null;
		if ($to->decodeType() != $from->decodeType())
		{
			$Field->type = $to->decodeType();
			$Field->forceChange();
		}
		return $Field;
	}

}
