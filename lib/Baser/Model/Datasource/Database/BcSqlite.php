<?php
/* SVN FILE: $Id$ */
/**
 * SQLite DBO拡張
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.datasources.dbo
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('DboSource', 'Model/Datasource');
App::uses('Sqlite', 'Model/Datasource/Database');
App::uses('CakeSchema', 'Model');
/**
 * SQLite DBO拡張
 *
 * @package Baser.Model.datasources.dbo
 */
class BcSqlite extends Sqlite {

/**
 * Enter description here...
 *
 * @var string
 * @access public
 */
	public $description = "SQLite3 DBO Driver";
/**
 * Enter description here...
 *
 * @var string
 * @access public
 */
	public $startQuote = '"';
/**
 * Enter description here...
 *
 * @var string
 * @access public
 */
	public $endQuote = '"';
/**
 * Base configuration settings for SQLite3 driver
 *
 * @var array
 * @access protected
 */
	protected $_baseConfig = array(
		'persistent' => false,
		'database' => null,
		'connect' => 'sqlite' //sqlite3 in pdo_sqlite is sqlite. sqlite2 is sqlite2
	);
/**
 * SQLite3 column definition
 *
 * @var array
 * @access public
 */
	public $columns = array(
		'primary_key' => array('name' => 'integer primary key autoincrement'),
		'string' => array('name' => 'varchar', 'limit' => '255'),
		'text' => array('name' => 'text'),
		'integer' => array('name' => 'integer', 'limit' => null, 'formatter' => 'intval'),
		'float' => array('name' => 'float', 'formatter' => 'floatval'),
		'datetime' => array('name' => 'datetime', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
		'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
		'binary' => array('name' => 'blob'),
		'boolean' => array('name' => 'boolean')
	);
	public $last_error = NULL;
	public $pdo_statement = NULL;
	public $rows = NULL;
	public $row_count = NULL;
/**
 * Connects to the database using config['database'] as a filename.
 *
 * @param array $config Configuration array for connecting
 * @return mixed
 * @access public
 */
	public function connect() {
		
		//echo "runs connect\n";
		$this->last_error = null;
		$config = $this->config;
		//$this->_connection = $config['connect']($config['database']);
		try {
			$this->_connection = new PDO($config['connect'].':'.$config['database']);
			$this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//$this->connected = is_resource($this->_connection);
			$this->connected = is_object($this->_connection);
		}
		catch(PDOException $e) {
			$this->last_error = array('Error connecting to database.',$e->getMessage());
		}
		return $this->connected;
		
	}
/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 */
	public function disconnect() {
		
		//echo "runs disconnect\n";
		//@sqlite3_close($this->_connection);
		$this->_connection = NULL;
		$this->connected = false;
		return $this->connected;
		
	}
/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 * @access protected
 */
	protected function _execute($sql, $params = array(), $prepareOptions = array()) {
		
		//echo "runs execute\n";
		//return sqlite3_query($this->_connection, $sql);

		for ($i = 0; $i < 2; $i++) {
			try {
				$this->last_error = NULL;
				$this->pdo_statement = $this->_connection->query($sql);
				if (is_object($this->pdo_statement)) {
					$this->rows = $this->pdo_statement->fetchAll(PDO::FETCH_NUM);
					$this->row_count = count($this->rows);
					return $this->pdo_statement;
				}
			}
			catch(PDOException $e) {
				// Schema change; re-run query
				if ($e->errorInfo[1] === 17) continue;
				$this->last_error = $e->getMessage();
			}
		}
		return false;
		
	}
/**
 * Returns an array of tables in the database. If there are no tables, an error is raised and the application exits.
 *
 * @return array Array of tablenames in the database
 * @access public
 */
	public function listSources($data = NULL) {
		
		//echo "runs listSources\n";
		$db = $this->config['database'];
		$this->config['database'] = basename($this->config['database']);

		$cache = parent::listSources();
		if ($cache != null) {
			// >>> ADD 2010/03/19 egashira
			// 接続をフルパスに戻す
			$this->config['database'] = $db;
			// <<<
			return $cache;
		}

		//echo "listsources:beforeresult ";
		// >>> CUSTOMIZE MODIFY 2010/12/26 ryuring
		//$result = $this->fetchAll("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;", false);
		// ---
		$result = $this->fetchAll("SELECT name FROM sqlite_master WHERE type='table' AND name<>'sqlite_sequence' ORDER BY name;", false);
		// <<<
		//echo "listsources:result ";
		//pr($result);

		if (!$result || empty($result)) {
			// >>> ADD 2010/03/19 egashira
			// 接続をフルパスに戻す
			$this->config['database'] = $db;
			// <<<
			return array();
		} else {
			$tables = array();
			foreach ($result as $table) {
				$tables[] = $table[0]['name'];
			}
			parent::listSources($tables);

			$this->config['database'] = $db;
			return $tables;
		}
		$this->config['database'] = $db;
		return array();
		
	}
	
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column
 * @param int $safe
 * @return string Quoted and escaped
 * @access public
 */
	public function value ($data, $column = null, $safe = false) {
		
		$parent = parent::value($data, $column, $safe);

		if ($parent != null) {
			return $parent;
		}

		if ($data === null) {
			return 'NULL';
		}

		switch ($column) {
			case 'boolean':
				if ($data === '') {
					return 0;
				}
				$data = $this->boolean((bool)$data);
				break;
			case 'integer';
				if ($data === '') {
					return 'NULL';
				}
				break;
			case 'datetime':
				if($data) {
					$data = trim(str_replace('/', '-', $data));
				}
				if ($data === '' || $data == '0000-00-00 00:00:00') {
					return "''";
				}
				break;
			default:
				if ($data === '') {
					return "''";
				}
				$data = $this->_connection->quote($data);
				return $data;
				break;
		}
		return "'" . $data . "'";
		
	}
/**
 * Generates and executes an SQL UPDATE statement for given model, fields, and values.
 *
 * @param Model $model
 * @param array $fields
 * @param array $values
 * @param mixed $conditions
 * @return array
 * @access public
 */
	public function update(Model $model, $fields = NULL, $values = NULL, $conditions = NULL) {
		
		if (empty($values) && !empty($fields)) {
			foreach ($fields as $field => $value) {
				if (strpos($field, $model->alias . '.') !== false) {
					unset($fields[$field]);
					$field = str_replace($model->alias . '.', "", $field);
					$field = str_replace($model->alias . '.', "", $field);
					$fields[$field] = $value;
				}
			}
		}
		return parent::update($model, $fields, $values, $conditions);
		
	}
/**
 * Begin a transaction
 * TODO データベースがロックされてしまい正常に処理が実行されないのでとりあえず未実装とする
 * ロックに関する原因については未解析
 * 
 * @param string $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions).
 * @access public
 */
	public function begin () {
		
		return null;
		/*if (parent::begin($model)) {
			if ($this->_connection->beginTransaction()) {
				$this->_transactionStarted = true;
				return true;
			}
		}
		return false;*/
		
	}
/**
 * Commit a transaction
 * TODO データベースがロックされてしまい正常に処理が実行されないのでとりあえず未実装とする
 * ロックに関する原因については未解析
 * 
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 * @access public
 */
	public function commit () {
		
		return null;
		/*if (parent::commit($model)) {
			$this->_transactionStarted = false;
			return $this->_connection->commit();
		}
		return false;*/
		
	}
/**
 * Rollback a transaction
 * TODO データベースがロックされてしまい正常に処理が実行されないのでとりあえず未実装とする
 * ロックに関する原因については未解析
 * 
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 * @access public
 */
	public function rollback () {
		
		return null;
		/*if (parent::rollback($model)) {
			return $this->_connection->rollBack();
		}
		return false;*/
		
	}

/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message
 * @access public
 */
	public function lastError(PDOStatement $query = null) {
		
		return $this->last_error;
		
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists, this returns false.
 *
 * @return integer Number of affected rows
 * @access public
 */
	public function lastAffected($source = NULL) {
		
		if ($this->_result) {
			return $this->pdo_statement->rowCount();
		}
		return false;
		
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 *
 * @return integer Number of rows in resultset
 * @access public
 */
	public function lastNumRows($source = NULL) { 
		
		if ($this->pdo_statement) {
			// pdo_statement->rowCount() doesn't work for this case
			return $this->row_count;
		}
		return false;
		
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @return int
 * @access public
 */
	public function lastInsertId($source = NULL) {
		
		//return sqlite3_last_insert_rowid($this->_connection);
		return $this->_connection->lastInsertId($source);
		
	}
/**
 * Converts database-layer column types to basic types
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 * @access public
 */
	public function column($real) {
		
		if (is_array($real)) {
			$col = $real['name'];
			if (isset($real['limit'])) {
				$col .= '('.$real['limit'].')';
			}
			return $col;
		}

		$col = strtolower(str_replace(')', '', $real));
		$limit = null;
		@list($col, $limit) = explode('(', $col);

		if (in_array($col, array('text', 'integer', 'float', 'boolean', 'timestamp', 'date', 'datetime', 'time'))) {
			return $col;
		}
		if (strpos($col, 'varchar') !== false || strpos($col, 'char') !== false) {
			return 'string';
		}
		if (in_array($col, array('blob', 'clob'))) {
			return 'binary';
		}
		if (strpos($col, 'numeric') !== false) {
			return 'float';
		}
		return 'text';
		
	}
/**
 * Enter description here...
 *
 * @param unknown_type $results
 * @return string
 * @access public
 */
	public function resultSet($results) {
		
		$this->results = $results;
		$this->map = array();
		$numFields = $results->columnCount();
		$index = 0;
		$j = 0;

		//PDO::getColumnMeta is experimental and does not work with sqlite3,
		//	so try to figure it out based on the querystring
		$querystring = $results->queryString;
		if (stripos($querystring, 'SELECT') === 0) {
			$last = strripos($querystring, 'FROM');
			if ($last !== false) {
				$selectpart = substr($querystring, 7, $last - 8);
				$selects = String::tokenize($selectpart, ',', '(', ')');
			}
		} elseif (strpos($querystring, 'PRAGMA table_info') === 0) {
			$selects = array('cid', 'name', 'type', 'notnull', 'dflt_value', 'pk');
		} elseif (strpos($querystring, 'PRAGMA index_list') === 0) {
			$selects = array('seq', 'name', 'unique');
		} elseif (strpos($querystring, 'PRAGMA index_info') === 0) {
			$selects = array('seqno', 'cid', 'name');
		}
		while ($j < $numFields) {
			if (!isset($selects[$j])) {
				$j++;
				continue;
			}
			if (preg_match('/\bAS\s+(.*)/i', $selects[$j], $matches)) {
				$columnName = trim($matches[1], '"');
			} else {
				$columnName = trim(str_replace('"', '', $selects[$j]));
			}

			if (strpos($selects[$j], 'DISTINCT') === 0) {
				$columnName = str_ireplace('DISTINCT', '', $columnName);
			}

			$metaType = false;
			try {
				$metaData = (array)$results->getColumnMeta($j);
				if (!empty($metaData['sqlite:decl_type'])) {
					$metaType = trim($metaData['sqlite:decl_type']);
				}
			} catch (Exception $e) {
			}

			if (strpos($columnName, '.')) {
				$parts = explode('.', $columnName);
				$this->map[$index++] = array(trim($parts[0]), trim($parts[1]), $metaType);
			} else {
				$this->map[$index++] = array(0, $columnName, $metaType);
			}
			$j++;
		}
		
	}

/**
 * Fetches the next row from the current result set
 *
 * @return unknown
 * @access public
 */
	public function fetchResult() {
		
		//if ($row = sqlite3_fetch_array($this->results, SQLITE3_ASSOC)) {
		if (count($this->rows)) {
			$row = array_shift($this->rows);
			//echo "fetchResult:nextrow ";
			//pr($row);
			$resultRow = array();
			$i = 0;

			foreach ($row as $index => $field) {
				//pr($index);
				if (isset($this->map[$index]) and $this->map[$index] != "") {
					//echo "asdf: ".$this->map[$index];
					list($table, $column) = $this->map[$index];
					$resultRow[$table][$column] = $row[$index];
				} else {
					$resultRow[0][str_replace('"', '', $index)] = $row[$index];
				}
				$i++;
			}
			//pr($resultRow);
			return $resultRow;
		} else {
			return false;
		}
		
	}
/**
 * Returns a limit statement in the correct format for the particular database.
 *
 * @param integer $limit Limit of results returned
 * @param integer $offset Offset from which to start results
 * @return string SQL limit/offset statement
 * @access public
 */
	public function limit ($limit, $offset = null) {
		
		if ($limit) {
			$rt = '';
			if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
				$rt = ' LIMIT';
			}
			$rt .= ' ' . $limit;
			if ($offset) {
				$rt .= ' OFFSET ' . $offset;
			}
			return $rt;
		}
		return null;
		
	}
/**
 * Generate a database-native column schema string
 *
 * @param array $column An array structured like the following: array('name'=>'value', 'type'=>'value'[, options]),
 * where options can be 'default', 'length', or 'key'.
 * @return string
 * @access public
 */
	public function buildColumn($column) {
		
		$name = $type = null;
		$column = array_merge(array('null' => true), $column);
		extract($column);

		if (empty($name) || empty($type)) {
			trigger_error('Column name or type not defined in schema', E_USER_WARNING);
			return null;
		}

		if (!isset($this->columns[$type])) {
			trigger_error("Column type {$type} does not exist", E_USER_WARNING);
			return null;
		}

		$real = $this->columns[$type];
		if (isset($column['key']) && $column['key'] == 'primary') {
			$out = $this->name($name) . ' ' . $this->columns['primary_key']['name'];
		} else {
			$out = $this->name($name) . ' ' . $real['name'];

			if (isset($real['limit']) || isset($real['length']) || isset($column['limit']) || isset($column['length'])) {
				if (isset($column['length'])) {
					$length = $column['length'];
				} elseif (isset($column['limit'])) {
					$length = $column['limit'];
				} elseif (isset($real['length'])) {
					$length = $real['length'];
				} else {
					$length = $real['limit'];
				}
				$out .= '(' . $length . ')';
			}
			if (isset($column['key']) && $column['key'] == 'primary') {
				$out .= ' NOT NULL';
			} elseif (isset($column['default']) && isset($column['null']) && $column['null'] == false) {
				$out .= ' DEFAULT ' . $this->value($column['default'], $type) . ' NOT NULL';
			} elseif (isset($column['default'])) {
				$out .= ' DEFAULT ' . $this->value($column['default'], $type);
			} elseif (isset($column['null']) && $column['null'] == true) {
				$out .= ' DEFAULT NULL';
			} elseif (isset($column['null']) && $column['null'] == false) {
				$out .= ' NOT NULL';
			}
		}
		return $out;
		
	}

/**
 * Removes redundant primary key indexes, as they are handled in the column def of the key.
 *
 * @param array $indexes
 * @param string $table
 * @return string
 */
	public function buildIndex($indexes, $table = null) {
		
		$join = array();

		foreach ($indexes as $name => $value) {
			if ($name == 'PRIMARY') {
				continue;
			} else {
				$out = 'CREATE ';
				if (!empty($value['unique'])) {
					$out .= 'UNIQUE ';
				}
				if (is_array($value['column'])) {
					$value['column'] = join(', ', array_map(array(&$this, 'name'), $value['column']));
				} else {
					$value['column'] = $this->name($value['column']);
				}
				$out .= "INDEX {$name} ON {$table}({$value['column']});";
			}
			$join[] = $out;
		}
		return $join;
		
	}

/**
 * Overrides DboSource::renderStatement to handle schema generation with SQLite3-style indexes
 *
 * @param string $type
 * @param array $data
 * @return string
 */
	public function renderStatement($type, $data) {
		
		switch (strtolower($type)) {
			case 'schema':
				extract($data);

				foreach (array('columns', 'indexes') as $var) {
					if (is_array(${$var})) {
						${$var} = "\t" . join(",\n\t", array_filter(${$var}));
					}
				}

				return "CREATE TABLE {$table} (\n{$columns});\n{$indexes}";
			break;
			default:
				return parent::renderStatement($type, $data);
			break;
		}
		
	}

	/**
	 * PDO deals in objects, not resources, so overload accordingly.
	 */
	public function hasResult() {
		
		return is_object($this->_result);
		
	}
/**
 * Generate a MySQL Alter Table syntax for the given Schema comparison
 *
 * @param array $compare Result of a CakeSchema::compare()
 * @return array Array of alter statements to make.
 * @access public
 */
	public function alterSchema($compare, $table = null) {
		
		if (!is_array($compare)) {
			return false;
		}
		$out = '';
		$colList = array();
		foreach ($compare as $curTable => $types) {
			$indexes = array();
			if (!$table || $table == $curTable) {
				$out .= 'ALTER TABLE ' . $this->fullTableName($curTable) . " \n";
				foreach ($types as $type => $column) {
					if (isset($column['indexes'])) {
						$indexes[$type] = $column['indexes'];
						unset($column['indexes']);
					}
					switch ($type) {
						case 'add':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$alter = 'ADD '.$this->buildColumn($col);
								$colList[] = $alter;
							}
						break;
						case 'drop':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$colList[] = 'DROP '.$this->name($field);
							}
						break;
						case 'change':
							foreach ($column as $field => $col) {
								if (!isset($col['name'])) {
									$col['name'] = $field;
								}
								$colList[] = 'CHANGE '. $this->name($field).' '.$this->buildColumn($col);
							}
						break;
					}
				}
				$colList = array_merge($colList, $this->_alterIndexes($curTable, $indexes));
				$out .= "\t" . implode(",\n\t", $colList) . ";\n\n";
			}
		}
		return $out;
		
	}
/**
 * Overrides DboSource::index to handle SQLite indexe introspection
 * Returns an array of the indexes in given table name.
 *
 * @param string $model Name of model to inspect
 * @return array Fields in table. Keys are column and unique
 * @access public
 */
	public function index($model) {
		
		$index = array();
		$table = $this->fullTableName($model, false, false);
		if ($table) {

			$tableInfo = $this->query('PRAGMA table_info(' . $table . ')');
			$primary = array();
			foreach($tableInfo as $info) {
				if(!empty($info[0]['pk'])){
					$primary = array('PRIMARY' => array('unique' => true, 'column' => $info[0]['name']));
				}
			}

			$indexes = $this->query('PRAGMA index_list(' . $table . ')');
			foreach ($indexes as $i => $info) {
				$key = array_pop($info);
				$keyInfo = $this->query('PRAGMA index_info("' . $key['name'] . '")');
				foreach ($keyInfo as $keyCol) {
					if (!isset($index[$key['name']])) {
						$col = array();
						$index[$key['name']]['column'] = $keyCol[0]['name'];
						$index[$key['name']]['unique'] = intval($key['unique'] == 1);
					} else {
						if (!is_array($index[$key['name']]['column'])) {
							$col[] = $index[$key['name']]['column'];
						}
						$col[] = $keyCol[0]['name'];
						$index[$key['name']]['column'] = $col;
					}
				}
			}
			$index = am($primary, $index);
		}
		return $index;
		
	}
/**
 * Generate index alteration statements for a table.
 * TODO 未サポート
 * 
 * @param string $table Table to alter indexes for
 * @param array $new Indexes to add and drop
 * @return array Index alteration statements
 * @access protected
 */
	protected function _alterIndexes($table, $indexes) {
		
		return array();
		
	}
/**
 * テーブル構造を変更する
 *
 * @param array $options [ new / old ]
 * @return boolean
 * @access public
 */
	public function alterTable($options) {

		extract($options);

		if(!isset($old) || !isset($new)){
			return false;
		}

		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$compare = $Schema->compare($old, $new);

		if(!$compare) {
			return false;
		}

		foreach($compare as $table => $types) {
			if(!$types){
				return false;
			}
			foreach($types as $type => $fields) {
				if(!$fields){
					return false;
				}
				foreach($fields as $fieldName => $column) {
					switch ($type) {
						case 'add':
							if(!$this->addColumn(array('field'=>$fieldName,'table'=>$table, 'column'=>$column))){
								return false;
							}
							break;
						case 'change':
							// TODO 未実装
							// SQLiteでは、SQLで実装できない？ので、フィールドの作り直しとなる可能性が高い
							// その場合、changeColumnメソッドをオーバライドして実装する
							return false;
							/*if(!$this->changeColumn(array('field'=>$fieldName,'table'=>$table, 'column'=>$column))){
								return false;
							}*/
							break;
						case 'drop':
							if(!$this->dropColumn(array('field'=>$fieldName,'table'=>$table))){
								return false;
							}
							break;
					}
				}
			}
		}

		return true;

	}
/**
 * テーブル名のリネームステートメントを生成
 *
 * @param string $sourceName
 * @param string $targetName
 * @return string
 * @access public
 */
	public function buildRenameTable($sourceName, $targetName) {
		
		return "ALTER TABLE ".$sourceName." RENAME TO ".$targetName;
		 
	}
/**
 * カラムを変更する
 * 
 * @param	array	$options [ table / new / old ]
 * @return boolean
 * @access public
 */
	public function renameColumn($options) {

		extract($options);

		if(!isset($table) || !isset($new) || !isset($old)) {
			return false;
		}

		$prefix = $this->config['prefix'];
		$_table = $table;
		$model = Inflector::classify(Inflector::singularize($table));
		$table = $prefix . $table;

		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$schema = $Schema->read(array('models'=>array($model)));
		$schema = $schema['tables'][$_table];

		$this->execute('BEGIN TRANSACTION;');

		// リネームして一時テーブル作成
		if(!$this->renameTable(array('old'=>$_table, 'new'=>$_table.'_temp'))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// スキーマのキーを変更（並び順を変えないように）
		$newSchema = array();
		foreach($schema as $key => $field) {
			if($key == $old) {
				$key = $new;
			}
			$newSchema[$key] = $field;
		}

		// フィールドを変更した新しいテーブルを作成
		if(!$this->createTable(array('schema'=>$newSchema, 'table'=>$_table))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// データの移動
		unset($schema['indexes']);
		$sql = 'INSERT INTO '.$table.' SELECT '.$this->_convertCsvFieldsFromSchema($schema).' FROM '.$table.'_temp';
		$sql = str_replace($old,$old.' AS '.$new, $sql);
		if(!$this->execute($sql)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// 一時テーブルを削除
		// dropTableメソッドはモデルありきなので利用できない
		if(!$this->execute('DROP TABLE '.$table.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		$this->execute('COMMIT;');
		return true;

	}
/**
 * カラムを削除する
 * 
 * @param	array	$options [ table / field / prefix ]
 * @return boolean
 * @access public
 */
	public function dropColumn($options) {

		extract($options);

		if(!isset($table) || !isset($field)) {
			return false;
		}

		if(!isset($prefix)){
			$prefix = $this->config['prefix'];
		}
		$_table = $table;
		$model = Inflector::classify(Inflector::singularize($table));
		$table = $prefix . $table;

		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$schema = $this->readSchema($_table);
		//$schema = $Schema->read(array('models'=>array($model)));
		$schema = $schema['tables'][$_table];

		$this->execute('BEGIN TRANSACTION;');

		// リネームして一時テーブル作成
		if(!$this->renameTable(array('old'=>$_table, 'new'=>$_table.'_temp'))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// フィールドを削除した新しいテーブルを作成
		unset($schema[$field]);
		if(!$this->createTable(array('schema'=>$schema, 'table'=>$_table))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// データの移動
		unset($schema['indexes']);
		if(!$this->_moveData($table.'_temp',$table,$schema)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// 一時テーブルを削除
		// dropTableメソッドはモデルありきなので利用できない
		if(!$this->execute('DROP TABLE '.$table.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		$this->execute('COMMIT;');
		return true;

	}
/**
 * テーブルからテーブルへデータを移動する
 * @param string	$sourceTableName
 * @param string	$targetTableName
 * @param array	$schema
 * @return booelan
 * @access protected
 */
	protected function _moveData($sourceTableName,$targetTableName,$schema) {
		
		$sql = 'INSERT INTO '.$targetTableName.' SELECT '.$this->_convertCsvFieldsFromSchema($schema).' FROM '.$sourceTableName;
		return $this->execute($sql);
		
	}
/**
 * スキーマ情報よりCSV形式のフィールドリストを取得する
 * @param array $schema
 * @return string
 * @access protected
 */
	protected function _convertCsvFieldsFromSchema($schema) {
		
		$fields = '';
		foreach($schema as $key => $field) {
			if($key != 'tableParameters') {
				$fields .= '"'.$key.'",';
			}
		}
		return substr($fields,0,strlen($fields)-1);
	}
/**
 * Returns an array of the fields in given table name.
 *
 * @param string $tableName Name of database table to inspect
 * @return array Fields in table. Keys are name and type
 * @access public
 */
	public function describe($model) {
		
		$cache = $this->__describe($model);
		if ($cache != null) {
			return $cache;
		}
		$fields = array();
		$result = $this->fetchAll('PRAGMA table_info(' . $model->tablePrefix . $model->table . ')');

		foreach ($result as $column) {
			$fields[$column[0]['name']] = array(
				'type'		=> $this->column($column[0]['type']),
				'null'		=> !$column[0]['notnull'],
				'default'	=> $column[0]['dflt_value'],
			// >>> CUSTOMIZE MODIFY 2010/11/24 ryuring
			// sqlite_sequence テーブルの場合、typeがないのでエラーとなるので調整
			//	'length'	=> $this->length($column[0]['type'])
			// ---
				'length'	=> ($column[0]['type'])? $this->length($column[0]['type']) : ''
			// <<<
			);
			// >>> CUSTOMIZE ADD 2010/10/27 ryuring
			// SQLiteではdefaultのNULLが文字列として扱われてしまう様子
			if($fields[$column[0]['name']]['default']=='NULL'){
				$fields[$column[0]['name']]['default'] = NULL;
			}
			// >>> CUSTOMIZE ADD 2011/08/22 ryuring
			if($fields[$column[0]['name']]['type']=='boolean' && $fields[$column[0]['name']]['default'] == "'1'") {
				$fields[$column[0]['name']]['default'] = 1;
			} elseif($fields[$column[0]['name']]['type']=='boolean' && $fields[$column[0]['name']]['default'] == "'0'") {
				$fields[$column[0]['name']]['default'] = 0;
			}
			// >>>
			if($column[0]['pk'] == 1) {
				$fields[$column[0]['name']] = array(
					'type'		=> $fields[$column[0]['name']]['type'],
					'null'		=> false,
					'default'	=> $column[0]['dflt_value'],
					'key'		=> $this->index['PRI'],
					// >>> CUSTOMIZE MODIFY 2010/03/23 ryuring
					// baserCMSのプライマリーキーの初期値は8バイトで統一
					//'length'	=> 11
					// ---
					'length' => 8
					// <<<
				);
			}
		}

		$this->_cacheDescription($model->tablePrefix . $model->table, $fields);
		return $fields;
		
	}
/**
 * Returns a Model description (metadata) or null if none found.
 * DboSQlite3のdescribeメソッドを呼び出さずにキャッシュを読み込む為に利用
 * Datasource::describe と同じ
 * 
 * @param Model $model
 * @return mixed
 * @access private
 */
	private function __describe($model) {
		
		if ($this->cacheSources === false) {
			return null;
		}
		$table = $this->fullTableName($model, false);
		if (isset($this->__descriptions[$table])) {
			return $this->__descriptions[$table];
		}
		$cache = $this->_cacheDescription($table);

		if ($cache !== null) {
			$this->__descriptions[$table] = $cache;
			return $cache;
		}
		return null;
		
	}

}