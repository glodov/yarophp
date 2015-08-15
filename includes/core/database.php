<?

namespace Core;

use \Helper\Console as Console;
use \Core\Database\Schema\Diff as Diff;
use \Core\Database\Schema\Field as Field;
use \Core\Database\Schema\Index as Index;
use \Core\Database\Schema\Table as Table;

/**
 * The Database class.
 *
 * @author Yarick.
 * @version 0.2
 */
class Database
{

	private $log = [];

	private $dbn, $lastQuery, $currentError,
		$name, $user, $host, $password, $persistent, $charset, $mode, $collation;

	private static $logDisabled = false;
	private static $instance;

	/**
	 * The singleton class constructor.
	 *
	 * @access private
	 */
	private function __construct(array $data = [])
	{
		$options = ['name', 'user', 'password', 'host', 'persistent', 'charset', 'collation', 'mode'];
		foreach ($options as $key)
		{
			if (array_key_exists($key, $data))
			{
				$this->$key = $data[$key];
			}
		}
		$this->open();
	}

	/**
	 * The clone function for class.
	 * Disabled to clone singleton.
	 *
	 * @access private.
	 */
	private function __clone()
	{
	}

	/**
	 * The function returns instance of current class.
	 *
	 * @static
	 * @access public
	 * @param array $options The options.
	 * @return object The Database object.
	 */
	public static function getInstance(array $options = [])
	{
		if ( self::$instance === null )
		{
            self::$instance = new self($options);
        }
        return self::$instance;
	}

	/**
	 * Disables/enables database logging.
	 * Use this to disable log with cron/parser to prevent RAM limit exceeding.
	 *
	 * @static
	 * @access public
	 * @param boolean $bool Disable value if TRUE then disabled, if FALSE then enabled.
	 */
	public static function disableLog($bool = true)
	{
		self::$logDisabled = (bool)$bool;
	}

	/**
	 * The function opens database connection.
	 * Halts script on error.
	 *
	 * @access public
	 */
	public function open()
	{
		$dsn = 'mysql:dbname=' . $this->name . ';host=' . $this->host . ';';
		$options = [];
		if ($this->persistent)
		{
			$options[\PDO::ATTR_PERSISTENT] = true;
		}

		try {
			$this->dbn = new \PDO($dsn, $this->user, $this->password, $options);
			$charset = $this->getDefaultCharset();
			if ($charset)
			{
				$this->execute('set names '.$charset);
			}
			$this->execute('set time_zone="' . date('P') . '"');
			$this->execute('set sql_mode="' . $this->mode . '"');
		} catch (\PDOException $e) {
			Console::log($e->getMessage());
			\Application::halt(500);
		}
	}

	public function getDefaultCharset()
	{
		if ($this->charset)
		{
			return $this->charset;
		}
		if ($this->collation)
		{
			$arr = explode('_', $this->collation);
			return $arr[0];
		}
		return null;
	}

	public function getDefaultCollation()
	{
		return $this->collation;
	}

	/**
	 * The function writes query to database log.
	 *
	 * @access protected
	 * @param string The query.
	 */
	protected function writeLog( $query )
	{
		$this->lastQuery = $query;
		if (self::$logDisabled)
		{
			return false;
		}
		$this->log[] = $query;
	}

	/**
	 * The function returns quoted string.
	 *
	 * @access public
	 * @param string $string The string.
	 * @return string The quoted string.
	 */
	public function quote( $string )
	{
		return $this->dbn->quote( $string );
	}

	/**
	 * The function returns sql quoted string (table/column names).
	 *
	 * @access public
	 * @param string $column The column or table name.
	 * @return string The quoted column.
	 */
	public function map( $column )
	{
		$result = [];
		foreach ( explode( '.', $column ) as $str )
		{
			$result[] = '`'.$str.'`';
		}
		return implode( '.', $result );
	}

	/**
	 * The function executes non select query.
	 *
	 * @access protected
	 * @param string $query The query.
	 * @return int The count of affected rows.
	 */
	public function execute( $query )
	{
		$this->writeLog( $query );
		return $this->dbn->exec( $query );
	}

	/**
	 * The function returns last inserted ID for auto increment field.
	 *
	 * @access public
	 * @return int The ID.
	 */
	public function getLastId()
	{
		return $this->dbn->lastInsertId();
	}

	/**
	 * The function execute selectable query.
	 *
	 * @access public
	 * @param string $query The query.
	 * @return array The array of queries.
	 */
	public function query( $query )
	{
		$this->writeLog( $query );
		$result = [];
		$stm = $this->dbn->query( $query );
		if ( $stm === false )
		{
			return $result;
		}
		$stm->setFetchMode( \PDO::FETCH_ASSOC );
		while ( $row = $stm->fetch() )
		{
			$result[] = $row;
		}
		return $result;
	}

	/**
	 * The function closes connection to database.
	 *
	 * @access public
	 * @return bool TRUE on success, FALSE on failure.
	 */
	public function close()
	{
		if ( !$this->dbn )
		{
			return false;
		}
		$this->dbn = null;
		return true;
	}

	/**
	 * The function deletes rows from table.
	 *
	 * @access public
	 * @param string $table The table name.
	 * @param array $params The clause.
	 * @return int The count of affected rows.
	 */
	public function delete( $table, $params = [] )
	{
		$this->currentError = '00000';
		if ( !count( $params ) )
		{
			return false;
		}
		$this->currentError = null;
		$query = 'delete from '.$table.' where 1 '.$this->sqlParams( $params );
		return $this->execute( $query );
	}

	/**
	 * The function updates rows in table.
	 *
	 * @access public
	 * @param string $table The table name.
	 * @param array $fields The fields values.
	 * @param array $params The clause.
	 * @return int The count of affected rows.
	 */
	public function update( $table, $fields = [], $params = [] )
	{
		$this->currentError = '00000';
		if ( !count( $params ) || !count( $fields ) )
		{
			return false;
		}
		$this->currentError = null;
		$primary = [];
		foreach ( $params as $value )
		{
			$arr = explode( ' ', $value, 3 );
			if ( count( $arr ) == 3 )
			{
				$primary[] = $arr[0];
			}
		}
		$values = [];
		foreach ( $fields as $name => $value )
		{
			if ( in_array( $name, $primary ) )
			{
				continue;
			}
			if ( is_array( $value ) || is_object( $value ) )
			{
				$value = serialize( $value );
			}
			$values[] = $this->map( $name ).' = '.$this->quote( $value );
		}
		$query = 'update '.$this->map( $table ).' set '.implode( ',', $values ).' where 1 '.$this->sqlParams( $params );
		return $this->execute( $query );
	}

	/**
	 * The function inserts row in table.
	 *
	 * @access public
	 * @param string $table The table name.
	 * @param array $fields The fields values.
	 * @return int The count of affected rows.
	 */
	public function insert( $table, $fields = [] )
	{
		$columns = [];
		$values = [];
		foreach ( $fields as $name => $value )
		{
			if ( is_array( $value ) || is_object( $value ) )
			{
				$value = serialize( $value );
			}
			$columns[] = $this->map( $name );
			$values[] = $this->quote( $value );
		}
		$sql = 'insert into '.$this->map( $table ).' ('.implode( ',', $columns ).') values ('.implode( ',', $values ).')';
		return $this->execute( $sql );
	}

	/**
	 * The function returns sql string of parameters - the clause.
	 *
	 * @access public
	 * @param array $params The parameters.
	 * @return string The parameters in sql string.
	 */
	public function sqlParams( $params = [] )
	{
		$clause = '';
		if ( is_array( $params ) )
		{
			foreach ( $params as $value )
			{
				if ( !$value )
				{
					continue;
				}
				if ( substr( $value, 0, 1 ) == '*' )
				{
					$clause .= ' and ('.substr( $value, 1 ).')';
				}
				else
				{
					$arr = explode( ' ', $value, 3 );
					if ( count( $arr ) != 3 )
					{
						continue;
					}
					$clause .= ' and '.$this->map( $arr[0] ).' '.$arr[1].' '.$this->quote( $arr[2] );
				}
			}
		}
		return $clause;
	}

	public function sqlSort( $sort = '' )
	{
		$query = '';
		if ( $sort )
		{
			if ( in_array( strtolower( $sort ), array('rand', 'rand()', 'random', 'random()') ) )
			{
				$query .= ' order by rand()';
			}
			else
			{
				$query .= ' order by '.$sort;
			}
		}
		return $query;
	}

	public function sqlLimit( $offset = null, $limit = null )
	{
		$query = '';
		if ( $offset !== null && $limit !== null )
		{
			$query .= ' limit '.intval( $offset ).', '.intval( $limit );
		}
		else if ( $limit !== null )
		{
			$query .= ' limit 0, '.intval( $limit );
		}
		return $query;
	}

	/**
	 * The function returns array of found rows by select from database.
	 *
	 * @access public
	 * @param string $table The table name.
	 * @param string $column The columns to fetch.
	 * @param array $param The search parameters.
	 * @param string $order The order.
	 * @param int $offset The offset.
	 * @param int $limit The limit.
	 * @return array The array of rows.
	 */
	public function select( $table, $columns = '*', $params = [], $order = null, $offset = null, $limit = null )
	{
		$query = 'select '.$columns.' from '.$this->map( $table ).' where 1 '
			.$this->sqlParams( $params ). $this->sqlSort( $order ).$this->sqlLimit( $offset, $limit );
		return $this->query( $query );
	}

	/**
	 * Sets up database changes into schema tables in current database.
	 *
	 * @access public
	 */
	public function setup()
	{
		$arr = \Application::getModels();
		$tables = [];
		foreach ($arr as $name)
		{
			$table = str_replace('\\Model\\', '\\Model\\Schema\\Table\\', $name);
			if (class_exists($table))
			{
				$tables[] = ['db' => new $table(), 'mo' => new $table()];
			}
		}

		$sql = [];
		foreach ($tables as $set)
		{
			$res = Diff::compare($set['mo']->setup(true), $set['db']->setup());

			Console::log(get_class($set['mo']));
			foreach ($res['create'] as $obj)
			{
				$query = $this->sqlCreate($set['mo'], $obj);
				if ($query)
				{
					Console::log('create > ' . $query);
					$sql[] = $query;
				}
			}
			foreach ($res['remove'] as $obj)
			{
				$query = $this->sqlRemove($set['mo'], $obj);
				if ($query)
				{
					Console::log('remove > ' . $query);
					$sql[] = $query;
				}
			}
			foreach ($res['change'] as $obj)
			{
				if ($obj instanceof Index)
				{
					$query = $this->sqlRemove($set['mo'], $obj);
					if ($query)
					{
						Console::log('remove > ' . $query);
						$sql[] = $query;
					}
					$query = $this->sqlCreate($set['mo'], $obj);
					if ($query)
					{
						Console::log('create > ' . $query);
						$sql[] = $query;
					}
				}
				else
				{
					$query = $this->sqlChange($set['mo'], $obj);
					if ($query)
					{
						Console::log('change > ' . $query);
						$sql[] = $query;
					}
				}
			}
		}
		foreach ($sql as $query)
		{
			$res = $this->execute($query);
			if (!$res)
			{
				$info = $this->dbn->errorInfo();
				if ('00000' != $info[0])
				{
					Console::log(implode(': ', $info));
				}
			}
		}
	}

	protected function sqlCreate(Table $Table, $Object)
	{
		$sql = '';
		if ($Object instanceof Table)
		{
			$sql = 'create table ' . $this->map($Table->getName()) . "(\n";
			foreach ($Table->getColumns() as $Field)
			{
				$sql .= $this->map($Field->name) . ' ' . $Field->sql() . ",\n";
			}
			foreach ($Table->getColumns(true) as $Index)
			{
				$sql .= $Index->sql() . ",\n";
			}
			$sql = rtrim($sql, ",\n") . "\n";
			$sql .= ')';
		}
		if ($Object instanceof Field)
		{
			$sql = 'alter table ' . $this->map($Table->getName()) . ' add column ' . $this->map($Object->name) . ' ' . $Object->sql();
		}
		if ($Object instanceof Index)
		{
			$sql = 'alter table ' . $this->map($Table->getName()) . ' add ' . $Object->sql();
		}
		return $sql;
	}

	protected function sqlRemove(Table $Table, $Object)
	{
		$sql = '';
		if ($Object instanceof Table)
		{
			$sql = 'drop table ' . $this->map($Table->getName());
		}
		if ($Object instanceof Field)
		{
			$sql = 'alter table ' . $this->map($Table->getName()) . ' drop column ' . $this->map($Object->name);
		}
		if ($Object instanceof Index)
		{
			$sql = 'alter table ' . $this->map($Table->getName()) . ' drop index ' . $this->map($Object->name);
		}
		return $sql;
	}

	protected function sqlChange(Table $Table, $Object)
	{
		$sql = '';
		if ($Object instanceof Field)
		{
			$sql = 'alter table ' . $this->map($Table->getName()) . ' change column ' . $this->map($Object->name)
			 	. ' ' . $this->map($Object->name) . ' ' . $Object->sql();
		}
		return $sql;
	}

	/**
	 * The function returns last error code.
	 *
	 * @access public
	 * @param bool $text If TRUE return as text, otherwise as code.
	 * @return string The last error code.
	 */
	public function getError( $text = false )
	{
		if ($this->currentError !== null)
		{
			return $this->currentError;
		}
		if ( $text )
		{
			$info = $this->dbn->errorInfo();
			return isset( $info[2] ) ? $info[2] : 'Database ERROR';
		}
		return $this->dbn->errorCode();
	}

	/**
	 * The function returns last query.
	 *
	 * @access public
	 * @return string The query.
	 */
	public function getLastQuery($count = 1)
	{
		if ($count > 1)
		{
			$offset = count($this->log) - $count;
			$arr = array_slice($this->log, $offset > 0 ? $offset : 0, $count);
			return array_reverse($arr);
		}
		return $this->lastQuery;
	}

	/**
	 * The function returns array of all queries executed in current object.
	 *
	 * @access public
	 * @return array The array of queries.
	 */
	public function getLog()
	{
		return $this->log;
	}

	/**
	 * The function returns status of connection.
	 *
	 * @access public
	 * @return string The status.
	 */
	public function __toString()
	{
		if ( $this->dbn )
		{
			return "Connected to MYSQL database\n";
		}
		return "Not connected\n";
	}

}
