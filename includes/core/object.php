<?

namespace Core;

/**
 * The Object class.
 * Using to access models in database.
 *
 * @abstract
 * @author Yarick.
 * @version 0.2
 */
abstract class Object
{

<<<<<<< HEAD
	private static $_instanceCount = 0,
					$_cache = [],
					$_cacheDisabled = false,
					$_dependencies_ids = [];

=======
	private static $instanceCount = 0,
					$cache = [],
					$cacheDisabled = false;
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

	private $_original = null;

	/**
	 * The function returns array of primary keys.
	 *
	 * @abstract
	 * @access protected
	 * @return array The primary keys array.
	 */
<<<<<<< HEAD
	protected function getPrimary()
	{
		$Table = $this->db()->getSchemaTable($this);
		return $Table->getPrimary();
	}
=======
	abstract protected function getPrimary();
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

	/**
	 * The function returns the model table name.
	 *
	 * @abstract
	 * @access protected
	 * @return string The table name.
	 */
<<<<<<< HEAD
	protected function getTableName()
	{
		$Table = $this->db()->getSchemaTable($this);
		return $Table->getName();
	}

	public function getEnum($field)
	{
		$Table = $this->db()->getSchemaTable($this);
		return $Table->getEnum($field);
	}
=======
	abstract protected function getTableName();
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c

	/**
	 * The object constructor.
	 *
	 * @access public
	 */
	public function __construct()
	{
<<<<<<< HEAD
		self::$_instanceCount++;
=======
		self::$instanceCount++;
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		foreach ($this->getObjectColumns() as $col => $class)
		{
			if (!$this->$col)
			{
				$this->$col = new $class();
			}
		}
	}

	/**
	 * The function returns Database object with current connection.
	 *
	 * @access protected
	 * @return object The Database object.
	 */
	protected function db()
	{
		return Database::getInstance();
	}

	protected function sqlTableName()
	{
		return $this->db()->map( $this->getTableName() );
	}

	/**
	 * Returns array of columns which stored as serialized objects.
	 *
	 * @access public
	 * @return array The columns.
	 */
	public function getObjectColumns()
	{
		return [];
	}

	/**
	 * The function sets object data array to object.
	 *
	 * @access public
	 * @param mixed $data The data to set, must be associated array or object.
	 * @param bool $forceColumns If TRUE sets columns for object even if they are not in object keys.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function set( $data, $forceColumns = false )
	{
		$result = array();
		if ( is_array( $data ) || is_object($data) )
		{
			foreach ( $data as $key => $value )
			{
				if ( $forceColumns || property_exists( $this, $key ) )
				{
					$this->$key = $value;
					$result[$key] = $value;
				}
			}
		}
		return $result;
	}

	/**
	 * The function sets object data from posted array.
	 *
	 * @access public
	 * @param array $data The posted data to set.
	 * @param mixed $only Fields which must be updated, if NULL updates all from data.
	 */
	public function setPost( array $data = array(), $only = null )
	{
		if (is_string($only))
		{
			$only = explode(',', $only);
		}
		if (is_array($only))
		{
			$result = array();
			foreach ($only as $col)
			{
				if (array_key_exists($col, $data))
				{
					$result[$col] = $data[$col];
				}
			}
			$data = $result;
		}
		return $this->set( $data );
	}

	/**
	 * The function returns object keys with their values.
	 *
	 * @access public
	 * @return array The object fields.
	 */
	public function getFields( $changedOnly = false )
	{
		$result = array();
		foreach ( get_object_vars( $this ) as $key => $value )
		{
			if ( substr( $key, 0, 1 ) != '_' )
			{
				if ( $changedOnly && is_array( $this->_original ) )
				{
					if ( $this->hasChanged( $key ) )
					{
						$result[ $key ] = $value;
					}
				}
				else
				{
					$result[ $key ] = $value;
				}
			}
		}
		ksort( $result );
		return $result;
	}

	protected function getEncodedFields($changedOnly = false)
	{
		$result = $this->getFields($changedOnly);
		$asObjects = $this->getObjectColumns();
		foreach ($asObjects as $col => $class)
		{
			if (array_key_exists($col, $result) && !is_scalar($result[$col]))
			{
				$result[$col] = serialize($result[$col]);
			}
		}
		return $result;
	}

	/**
	 * Returns current value of primary key.
	 *
	 * @access public
	 * @return mixed The primary value: string or array for complex primary key, NULL if primary not defined.
	 */
	public function id()
	{
		$key = $this->getPrimary();
		if (0 === count($key))
		{
			return null;
		}
		if (1 === count($key))
		{
			return $this->{$key[0]};
		}
		$result = [];
		foreach ($key as $name)
		{
			$result[$name] = $this->$name;
		}
		return $result;
	}

	/**
	 * The function returns TRUE if primary key is not set, otherwise FALSE.
	 *
	 * @access protected
	 * @return bool TRUE if primary key is empty, otherwise FALSE.
	 */
	protected function isPrimaryEmpty()
	{
		foreach ( $this->getPrimary() as $field )
		{
			if ( !empty( $this->$field ) )
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Disables cache for Object.
	 * Use this to disable log with cron/parser to prevent RAM limit exceeding.
	 *
	 * @static
	 * @access public
	 * @param boolean $bool If TRUE disables cache, if FALSE enables.
	 */
	public static function disableCache($bool = true)
	{
		self::$_cacheDisabled = (bool)$bool;
	}

	/**
	 * The function returns TRUE if auto cache is enabled for current object, otherwise FALSE.
	 *
	 * @access protected
	 * @return bool TRUE if auto cache is enabled.
	 */
	protected function hasAutoCache()
	{
		return true;
	}

	/**
	 * The function flushes (clear) current item cache data.
	 *
	 * @access protected
	 * @return bool TRUE on success, FALSE on failure.
	 */
	protected function flushCache()
	{
		if ( !property_exists( $this, 'Id' ) || !$this->Id )
		{
			return false;
		}
		if ( isset( self::$_cache[ get_class( $this ) ][ $this->Id ] ) )
		{
			self::$_cache[ get_class( $this ) ][ $this->Id ] = null;
			return true;
		}
		return false;
	}

	/**
	 * The function add cache object to global cache.
	 *
	 * @access protected
	 * @param object $Object The Object to add.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	protected function pushCache( Object $Object )
	{
		if (self::$_cacheDisabled)
		{
			return false;
		}
		if ( !$this->hasAutoCache() || !property_exists( $Object, 'Id' ) || !$Object->Id )
		{
			return false;
		}
		if ( !isset( self::$_cache[ get_class( $this ) ] ) )
		{
			self::$_cache[ get_class( $this ) ] = array();
		}
		self::$_cache[ get_class( $this ) ][ $Object->Id ] = $Object;
		return true;
	}

	/**
	 * The function returns Object from cache if it exists already.
	 *
	 * @access public
	 * @param array $params The clause.
	 * @return mixed The cached Object or FALSE.
	 */
	protected function popCache( $params = array() )
	{
		if ( $this->hasAutoCache() && count( $params ) == 1 )
		{
			foreach ( $params as $param )
			{
				$arr = explode( ' ', $param, 3 );
				if ( $arr[0] == 'Id' && $arr[1] == '=' && count( $arr ) == 3 )
				{
					if ( isset( self::$_cache[ get_class( $this ) ][ $arr[2] ] ) )
					{
						$clone = clone self::$_cache[ get_class( $this ) ][ $arr[2] ];
						return $clone;
					}
				}
			}
		}
		return false;
	}

<<<<<<< HEAD
	protected function beforeSave($newItem = false)
	{
	}

	protected function afterSave($saveResult)
	{

	}

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	/**
	 * The function saves current object to database.
	 *
	 * @access public
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function save()
	{
		$result = null;
		$this->beforeSave();
		if ( $this->isPrimaryEmpty() )
		{
			$result = $this->saveNew();
		}
		else
		{
			if ( $this->hasAutoCache() )
			{
				$this->flushCache();
			}
			$this->db()->update( $this->getTableName(), $this->getEncodedFields(true), $this->getPrimaryClause() );
<<<<<<< HEAD
			$result = $this->db()->getError() == '00000';
=======
			return $this->db()->getError() == '00000';
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		}
		$this->afterSave($result);
		return $result;
	}

	/**
	 * The function saves current object to database.
	 * Always saves as new object.
	 *
	 * @todo auto_increment works only with Id and id.
	 *
	 * @access public
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function saveNew()
	{
<<<<<<< HEAD
		$result = false;
		$this->beforeSave(true);
=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
		$this->db()->insert( $this->getTableName(), $this->getEncodedFields() ) > 0;
		if ( $this->db()->getError() == '00000' )
		{
			$auto = $this->getAutoincrementField();
			if ( in_array( $auto, array_keys( get_object_vars( $this ) ) ) && property_exists( $this, $auto ) )
			{
				$id = $this->db()->getLastId();
				if ( $id )
				{
					$this->$auto = $id;
				}
			}
			$Item = $this->findItem( $this->getPrimaryClause() ) ;
			$this->set( $Item->getFields() );
			$result = true;
		}
		$this->afterSave($result);
		return $result;
	}

	/**
	 * The function returns array of primary clause.
	 *
	 * @access protected
	 * @return array The primary clause array.
	 */
	protected function getPrimaryClause()
	{
		$result = array();
		foreach ( $this->getPrimary() as $field )
		{
			$result[] = $field.' = '.$this->$field;
		}
		return $result;
	}

	/**
	 * Returns default auto increment field name.
	 *
	 * @access protected
	 * @return string The field name.
	 */
	protected function getAutoIncrementField()
	{
		return 'id';
<<<<<<< HEAD
	}

	protected function myDependencies()
	{
		return [];
=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	}

	/**
	 * The function deletes current object from database.
	 *
	 * @access public
	 * @return bool TRUE on success, FALSE on failure
	 */
	public function drop()
	{
		$res = $this->dropList( $this->getPrimaryClause() );
		if ( $res )
		{
			$info = $this->getUploadFileInfo();
			if ( $info && count( $info ) )
			{
				\Helper\File::detach( $this );
			}
			foreach ($this->myDependencies() as $className => $column)
			{
				$className = '\\Model\\' . $className;
				$obj = new $className();
				$count = $obj->findSize([$column . ' = ' . $this->id]);
				while ($count > 0)
				{
					foreach ($obj->findList([$column . ' = ' . $this->id], null, 0, 100) as $object)
					{
						$object->drop();
					}
					$count -= 100;
				}
			}
		}
		return $res;
	}

	/**
	 * The function deletes object rows from database with passed clause.
	 *
	 * @access public
	 * @param array $params The clause.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function dropList( $params = array() )
	{
		return $this->db()->delete( $this->getTableName(), $params );
	}

	/**
	 * The function updates current object rows in database with passed clause.
	 *
	 * @access public
	 * @param array $fields The fields values.
	 * @param array $params The clause.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function updateList( array $fields, array $params = array() )
	{
		return $this->db()->update( $this->getTableName(), $fields, $params );
	}

<<<<<<< HEAD
	public function hasDependencies()
	{
		$name = get_class($this);
		if (!isset(self::$_dependencies_ids[$name]))
		{
			self::$_dependencies_ids[$name] = [];
			$linked = $this->myDependencies();
			if (!count($linked))
			{
				return false;
			}
			$sql = [];
			foreach ($linked as $className => $column)
			{
				$className = '\\Model\\' . $className;
				$obj = new $className();
				$obj->getTableName();
				$sql[] = 'SELECT distinct ' . $this->db()->map($column) . ' FROM ' . $this->db()->map($obj->getTableName());
			}
			$sql = implode(' UNION ', $sql);
			$arr = $this->db()->query($sql);
			foreach ($arr as $row)
			{
				self::$_dependencies_ids[$name][] = $row[$column];
			}
		}
		return in_array($this->id, self::$_dependencies_ids[$name]);
	}

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	/**
	 * The function prints out error if it occured.
	 *
	 * @access protected
	 */
	protected function showError()
	{
		if ( $this->db()->getError() != '00000' )
		{
			$arr = $this->db()->getLog();
			echo get_class( $this ).': '.end( $arr )."\n".$this->getError();
			exit;
		}
	}

	/**
	 * The function finds an object item with passed clause.
	 *
	 * @access public
	 * @param array $params The clause.
	 * @return object The current class object.
	 */
	public function findItem( $params = array() )
	{
		$Object = $this->popCache( $params );
		if ( $Object !== false )
		{
			return $Object;
		}
		$arr = $this->db()->select( $this->getTableName(), '*', $params, null, 0, 1 );
		$this->showError();
		$arr = $this->processDbResult( $arr, 'item' );
		if ( count( $arr ) )
		{
			$Object = array_pop( $arr );
			$this->pushCache( $Object );
		}
		else
		{
			$name = get_class( $this );
			$Object = new $name();
			$Object->init('item');
		}
		return $Object;
	}

	/**
	 * The function finds and object items with passed clause.
	 *
	 * @access public
	 * @param array $params The clause.
	 * @param string $order The order string.
	 * @param int $offset The offset.
	 * @param int $limit The limit.
	 * @return array The array of current class objects.
	 */
	public function findList( $params = array(), $order = null, $offset = null, $limit = null )
	{
		$arr = $this->db()->select( $this->getTableName(), '*', $params, $order, $offset, $limit );
		$this->showError();
		return $this->processDbResult( $arr, 'list' );
	}

	/**
	 * The function returns count of object rows with passed clause.
	 *
	 * @access public
	 * @param array $params The clause.
	 * @return int The count of rows.
	 */
	public function findSize( $params = array() )
	{
		$arr = $this->db()->select( $this->getTableName(), 'count(*) as count', $params );
		foreach ( $arr as $item )
		{
			if ( isset( $item['count'] ) )
			{
				return $item['count'];
			}
		}
		$this->showError();
		return null;
	}

	/**
	 * The function finds and object items with passed clause.
	 *
	 * @access public
	 * @param string $columns The columns to fetch.
	 * @param array $params The clause.
	 * @param string $order The order string.
	 * @param int $offset The offset.
	 * @param int $limit The limit.
	 * @return array The array of current class objects.
	 */
	public function findArray( $columns = '*', $params = array(), $order = null, $offset = null, $limit = null )
	{
		if ( !isset( $columns ) )
		{
			$columns = '*';
		}
		return $this->db()->select( $this->getTableName(), $columns, $params, $order, $offset, $limit );
	}

	/**
	 * The function finds and object items with passed clause.
	 *
	 * @access public
	 * @param string $columns The columns to fetch.
	 * @param array $params The clause.
	 * @param string $order The order string.
	 * @param int $offset The offset.
	 * @param int $limit The limit.
	 * @return array The array of current class objects.
	 */
	public function findResult( $columns = '*', $params = array(), $order = null, $offset = null, $limit = null )
	{
		$result = array();
		if ( !isset( $columns ) )
		{
			$columns = '*';
		}
		$arr = $this->db()->select( $this->getTableName(), $columns, $params, $order, $offset, $limit );
		$this->showError();
		return $this->processDbResult( $arr, 'result' );
	}

	public function findCopy($fields = null)
	{
		$params = $arr = array();
		$args = func_get_args();
		if ( is_array( $fields ) )
		{
			$arr = $fields;
		}
		else if ( count( $args ) )
		{
			$arr = $args;
		}
		else
		{
			$arr = $this->getPrimary();
		}
		foreach ( $arr as $key )
		{
			if ( property_exists( $this, $key ) )
			{
				$params[] = $key.' = '.$this->$key;
			}
		}
		return $this->findItem( $params );
	}

	/**
	 * The function returns TRUE if copy of current Object exists in database already.
	 *
	 * @access public
	 * @param mixed $fields The fields to check, by default it is primary keys.
	 * Can be array of field names or just many arguments.
	 * @return bool TRUE if copy exists, otherwise FALSE.
	 */
	public function hasCopy( $fields = null )
	{
		$params = $arr = array();
		$args = func_get_args();
		if ( is_array( $fields ) )
		{
			$arr = $fields;
		}
		else if ( count( $args ) )
		{
			$arr = $args;
		}
		else
		{
			$arr = $this->getPrimary();
		}
		foreach ( $arr as $key )
		{
			if ( property_exists( $this, $key ) )
			{
				$params[] = $key.' = '.$this->$key;
			}
		}
		return $this->findSize( $params ) > 0;
	}

	/**
	 * The function executes query an returns array of objects.
	 *
	 * @access public
	 * @param string $query The SQL query string.
	 * @param bool $forceColumns If TRUE sets columns for object even if they are not in object keys.
	 * @return array The result.
	 */
	public function query( $query, $forceColumns = false )
	{
		$arr = $this->db()->query( $query );
		$this->showError();
		return $this->processDbResult( $arr, 'list', null, $forceColumns );
	}

	/**
	 * The function processes database result to array of objects.
	 *
	 * @access protected
	 * @param array $data The database result data.
	 * @param string $type The type of triggered function: item | list | result.
	 * @param string $className The object class name, by default gets itself class name $this.
	 * @param bool $forceColumns If TRUE sets columns for object even if they are not in object keys.
	 */
	protected function processDbResult( array $data, $type = 'list', $className = null, $forceColumns = false )
	{
		if ( !$className )
		{
			$className = get_class( $this );
		}
		$result = array();
		$asObjects = $this->getObjectColumns();
		foreach ( $data as $item )
		{
			$Object = new $className();
			if (count($asObjects))
			{
				foreach ($item as $key => $value)
				{
					if (array_key_exists($key, $asObjects))
					{
						$item[$key] = unserialize($value);
					}
				}
			}
			$Object->set( $item, $forceColumns );
			$Object->init( $type );
			$result[] = $Object;
			if ( $type == 'item' )
			{
				break;
			}
		}
		return $result;
	}

	/**
	 * @see Database::getError()
	 */
	public function getError()
	{
		return $this->db()->getError( true );
	}

	/**
	 * The function returns array of file uploading rules (parameters).
	 *
	 * @access public
	 * @return array The info array.
	 */
	public function getUploadFileInfo()
	{
		return null;

		// as example
		return array(
			'sizes'			=> array( '800x600', '200x200' ),
			'forceResize'	=> array( true, false ),
			'quality'		=> array( 75, 90 ),
			'crop'			=> array( 'North', 'Center' ),
			'deny'			=> array( 'exe', 'php', 'pl', 'cgi', 'asp', 'php3', 'php4', 'php5' ),
			'allow'			=> array( 'jpg', 'gif', 'png' ),
			'folder'		=> 'my_object',
			'folderFormat'	=> '%05d',
			'folderLimit'	=> 1000,
			'urlFormat'		=> true,
			'timeAffix'		=> null,
			'dropOrig'		=> true,
			'extension'		=> 'jpg',
		);
	}

	/**
	 * The function returns file url for object.
	 *
	 * @access public
	 * @return string The file url.
	 */
	public function getFileUrl( $class, $folder, $index, $ext )
	{
		return null;
	}

	/**
	 * The event on initializing Object on getting from database.
	 *
	 * @access public
	 * @param string $type The type of triggered function: item | list | result.
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function init( $type = 'list' )
	{
		$this->_original = $this->getFields();
		return true;
	}

	protected function hasChanged( $field = null, $and = false )
	{
		if (is_array($field))
		{
			if ($and)
			{
				foreach ($field as $col)
				{
					if (!$this->hasChanged($col))
					{
						return false;
					}
				}
				return true;
			}
			else
			{
				foreach ($field as $col)
				{
					if ($this->hasChanged($col))
					{
						return true;
					}
				}
			}
			return false;
		}
		if ( $field === null )
		{
			$arr = $this->getFields(true);
			return count( $arr ) > 0;
		}
		else
		{
			return is_array($this->_original) && array_key_exists( $field, $this->_original ) && property_exists( $this, $field )
				&& self::trimValue( $this->_original[ $field ] ) !== self::trimValue( $this->$field );
		}
	}

	public function getOriginal( $field = null )
	{
		if ( $field === null )
		{
			return $this->_original;
		}
		return isset( $this->_original[ $field ] ) ? $this->_original[ $field ] : null;
	}

	protected function setOriginal( $name, $value = null )
	{
		if ( !is_array( $this->_original ) )
		{
			$this->_original = array();
		}
		$this->_original[ $name ] = $value;
	}

	public function getChangedFields()
	{
		$result = array();
		foreach ( $this->getFields() as $key => $value )
		{
			if ( $this->hasChanged( $key ) )
			{
				$result[ $key ] = $value;
			}
		}
		return $result;
	}

<<<<<<< HEAD
	protected function getFixedFieldsToArray()
	{
		return [];
	}

=======
>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	/**
	 * Returns object converted into array.
	 *
	 * @access public
<<<<<<< HEAD
	 * @param array $only The array of field names to export in array.
	 * @return array The object data.
	 */
	public function toArray($only = null)
	{
		$reflection = new \ReflectionObject($this);
		$result = [];
		$fixed = $this->getFixedFieldsToArray();
		$oc = $this->getObjectColumns();
		foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property)
		{
			$value = $property->getValue($this);
			$field = $property->getName();
			if (is_array($only) && !in_array($field, $only))
			{
				continue;
			}
			if (is_numeric($value) && !in_array($field, $fixed))
			{
				$value = floatval($value);
			}
			if (isset($oc[$field]))
			{
				$className = $oc[$field];
				if ($value instanceof $className)
				{
					// no need to do anything
				}
				else
				{
					$value = self::fromArray($value, $className);
				}
			}
			if (is_array($value))
			{
				foreach ($value as $key => $val)
				{
					if (is_object($val))
					{
						$val = method_exists($val, 'toArray') ? $val->toArray() : get_object_vars($val);
					}
					$value[$key] = $val;
				}
			}
			if (is_object($value))
			{
				$className = get_class($value);
				$value = method_exists($value, 'toArray') ? $value->toArray() : get_object_vars($value);
			}
			$result[$field] = $value;
		}
		$result['$class'] = get_class($this);
		return $result;
	}

	/**
	 * Loads object fields from array.
	 *
	 * @static
	 * @access public
	 * @param $data mixed The object data: array or object.
	 * @param $className The name of class, if NULL try to resolve it from data.
	 * @return mixed The object loaded from data or passed data if cannot be loaded into object.
	 */
	public static function fromArray($data, $className = null)
	{
		$object = null;
		if (is_array($data))
		{
			if (null === $className && isset($data['$class']))
			{
				$className = $data['$class'];
			}
			if ($className)
			{
				$data = (object)$data;
			}
		}
		if (is_object($data))
		{
			$object = (get_class($data) == 'stdClass') && $className ? new $className() : $data;
		}
		if (is_array($data) || is_object($data))
		{
			foreach ($data as $key => $value)
			{
				if (null === $object && is_array($value) && isset($value['$class']))
				{
					$object = (object)[];
				}
				if (null === $object || is_array($object))
				{
					$object[$key] = self::fromArray($value);
				}
				else
				{
					$object->$key = self::fromArray($value);
				}
			}
		}
		return $object ? $object : $data;
	}

=======
	 * @return array The object data.
	 */
	public function toArray()
	{
		$reflection = new ReflectionObject($this);
		$result = [];
		$fixed = $this->getFixedFieldsToArray();
		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
		{
			$value = $property->getValue($this);
			if (is_numeric($value) && !in_array($property->getName(), $fixed))
			{
				$value = floatval($value);
			}
			$result[$property->getName()] = $value;
		}
		return $result;
	}

>>>>>>> 6c8d365d76a90d18270293cbb397398dfec2b14c
	/**
	 * The function returns last field value in object list.
	 *
	 * @static
	 * @access public
	 * @param object $Object The Object.
	 * @param string $field The field name.
	 * @param array $params The clause.
	 * @return mixed The last field value.
	 */
	public static function getLast( Object $Object, $field, array $params = array() )
	{
		if ( !property_exists( $Object, $field ) )
		{
			return null;
		}
		foreach ( $Object->findList( $params, $field.' desc', 0, 1 ) as $Item )
		{
			return $Item->$field;
		}
		return null;
	}

	/**
	 * The function returns count of object instances.
	 *
	 * @static
	 * @access public
	 * @return int The count object instances.
	 */
	public static function getInstanceCount()
	{
		return self::$_instanceCount;
	}

	/**
	 * The function returns hashed password.
	 *
	 * @static
	 * @access public
	 * @param string $password The password.
	 * @return string The hashed password.
	 */
	public static function hashPassword( $password )
	{
		return sha1( $password );
	}

	/**
	 * The function returns associated array from array of Objects.
	 *
	 * @static
	 * @access public
	 * @param array $input The array of Objects.
	 * @param string $key The key field name.
	 * @param string $value The value field name.
	 * @return array The array.
	 */
	public static function convertArray( array $input, $key, $value )
	{
		$result = array();
		foreach ( $input as $Item )
		{
			if ( !$Item instanceof Object )
			{
				continue;
			}
			if ( property_exists( $Item, $key ) && property_exists( $Item, $value ) )
			{
				$result[ $Item->$key ] = $Item->$value;
			}
		}
		return $result;
	}

	public static function trimValue( $value )
	{
		if ( is_array( $value ) || is_object( $value ) )
		{
			return serialize( $value );
		}
		return trim( $value );
	}

	/**
	 * The function returns string representation of object.
	 *
	 * @access public
	 * @return string The object string.
	 */
	public function __toString()
	{
		$result = 'Object: '.get_class( $this )." {\n";
		foreach ( $this->getFields() as $name => $value )
		{
			$result .= sprintf( "\t%-20s%s\n", $name, is_scalar($value) ? $value : json_encode($value) );
		}
		$result .= "}\n";
		return $result;
	}

	public function __sleep()
	{
		$arr = array();
		foreach ( $this as $key => $value )
		{
			if ( $key != '_original' )
			{
				$arr[] = $key;
			}
		}
		return $arr;
	}

	public function __clone()
	{
		$this->init('item');
	}

	/**
	 * Returns the model of called class.
	 *
	 * @static
	 * @access public
	 * @return Object The model of called class.
	 */
	public static function model()
	{
		$name = get_called_class();
		return new $name();
	}

}
