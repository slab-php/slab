<?php

class DbMySql extends Database {
	var $connection = null;
	var $columnTypes = array(
		'primary_key'	=> array('formatter' => 'intval'),
		'string'	=> array('limit' => '255', 'default' => ''),
		'text'		=> array('default' => ''),
		'integer'	=> array('limit' => '11', 'formatter' => 'intval', 'default' => 0),
		'float'		=> array('formatter' => 'floatval', 'default' => 0),
		'datetime'	=> array('format' => 'Y-m-d H:i:s', 'formatter' => 'date', 'default' => '1970-01-01'),
		'timestamp'	=> array('format' => 'Y-m-d H:i:s', 'formatter' => 'date', 'default' => '1970-01-01'),
		'time'		=> array('format' => 'H:i:s', 'formatter' => 'date', 'default' => '0:0:0'),
		'date'		=> array('format' => 'Y-m-d', 'formatter' => 'date', 'default' => '1970-01-01'),
		'blob'		=> array(),
		'bool'		=> array('limit' => '1', 'default' => false)
	);
	
	function connect() {
		if (!empty($this->connection)) {
			$this->disconnect();
		}

		$this->port = empty($this->port) ? null : $this->port;
		
		$this->connection = new mysqli(
			$this->host,
			$this->login,
			$this->password,
			$this->database,
			$this->port);

		return $this->check_connection();
	}
	
	function disconnect() {
		if (empty($this->connection)) return;
		$this->connection->close();
		$this->connection = null;
	}

	function check_connection() {
		if (empty($this->connection)) {
			throw new Exception('Connection to the database has not been established');
		}

		if (mysqli_connect_error()) {
			throw new Exception('Database connection error ('.mysqli_connect_errno().') '.mysqli_connect_error());
		}

		return true;
	}
	
	function select($table, $fields = null, $where = null, $orderBy = null, $groupBy = null, $limit = null) {
		$fields = empty($fields) ? '*' : $fields;
		$where = empty($where) ? '' : "WHERE {$where}";
		$orderBy = empty($orderBy) ? '' : "ORDER BY {$orderBy}";
		$groupBy = empty($groupBy) ? '' : "GROUP BY {$groupBy}";
		$limit = empty($limit) ? '' : "LIMIT {$limit}";

		$sql = "SELECT {$fields} FROM {$this->tablePrefix}{$table} {$where} ${groupBy} {$orderBy} {$limit}";

		return $this->query($sql);
	}
	
	function query($sql) {
		$this->check_connection();

		$result = $this->connection->query($sql);

		if (!$result) {
			throw new Exception($this->connection->error.': '.h($sql));
		}
		
		if (!str_starts_with(uc($sql), array('SELECT', 'SHOW'))) {
			return $result;
		}
		
		$data = array();
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}

		$result->free();
		
		return $data;
	}
	
	// UPDATE $table SET ($data as x=y) WHERE $where
	// There is an assumption that data is already escaped
	function update($table, $data, $where) {
		$valueArray = array();
		foreach ($data as $key=>$value) {
			$valueArray[] = '`'.$key.'`='.$value;
		}

		$values = implode(', ', $valueArray);

		$sql = "UPDATE {$this->tablePrefix}{$table} SET {$values} WHERE {$where}";

		return $this->query($sql);
	}
	
	// INSERT INTO $table($data keys) VALUES($data values)
	// return the new id
	function insert($table, $data) {
		$dataKeys = implode('`, `', array_keys($data));
		$dataValues = implode(', ', array_values($data));

		$sql = "INSERT INTO {$this->tablePrefix}{$table}(`{$dataKeys}`) VALUES({$dataValues})";

		$this->query($sql);
		
		$this->id = $this->connection->insert_id;
		
		return $this->id;
	}
	
	// DELETE FROM $table WHERE $conditions
	function delete($table, $conditions = null) {
		$where = empty($conditions) ? '' : "WHERE {$conditions}";

		$sql = "DELETE FROM {$this->tablePrefix}{$table} {$where}";

		return $this->query($sql);
	}
	
	// This is based on CakePHP's DboMySql::value():
	//   Returns a quoted and escaped string of $data for use in an SQL statement.
 	function make_value_safe($data, $column = null) {
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		if ($data === null || (is_array($data) && empty($data))) {
			return 'NULL';
		} else if ($data === '' && $column !== 'integer' && $column !== 'float' && $column !== 'boolean') {
			return  "''";
		}
			
		if ($column == 'boolean') {
			if ($data === true || $data === false) {
				return ($data === true) ? 1 : 0;
			}
			return !empty($data) ? 1 : 0;
		} else if ($column == 'integer' || $column == 'float') {
			if ($data === '') {
				return 'NULL';
			}
			if (
					is_int($data) || 
					is_float($data) || 
					$data === '0' || 
					(is_numeric($data) && strpos($data, ',') === false && $data[0] != '0' && strpos($data, 'e') === false)
				) {
				return $data;
			}
		} else if ($column == 'blob') {
			return '0x'.bin2hex($data);
		}

		if (get_magic_quotes_gpc()) {
			$data = stripslashes($data);
		}
		
		return "'" . $this->connection->real_escape_string($data) . "'";
	}

	function get_table_schema($tableName) {
		$schema = array();

		$results = $this->query("SHOW COLUMNS FROM `{$tableName}`");

		foreach ($results as $row) {
			$schema[$row['Field']] = array();
			if ($row['Key'] == 'PRI') {
				$schema[$row['Field']]['type'] = 'primary_key';
			} else {
				$colType = str_replace(')','',$row['Type']);
				$colType = explode('(', $colType);
				if (count($colType) > 1) {
					$schema[$row['Field']]['limit'] = $colType[1];
				}
				$colType = $colType[0];
				switch ($colType) {
					case 'varchar':
					case 'char':
						$schema[$row['Field']]['type'] = 'string';
						break;
					case 'text':
					case 'tinytext':
					case 'mediumtext':
					case 'longtext':
						$schema[$row['Field']]['type'] = 'text';
						break;
					case 'tinyint':
					case 'smallint':
					case 'mediumint':
					case 'int':
					case 'bigint':
						$schema[$row['Field']]['type'] = 'integer';
						break;
					case 'float':
					case 'double':
					case 'decimal':
						$schema[$row['Field']]['type'] = 'float';
						break;
					case 'datetime':
						$schema[$row['Field']]['type'] = 'datetime';
						break;
					case 'timestamp':
						$schema[$row['Field']]['type'] = 'timestamp';
						break;
					case 'date':
						$schema[$row['Field']]['type'] = 'date';
						break;
					case 'time':
						$schema[$row['Field']]['type'] = 'time';
						break;
					case 'blob':
					case 'tinyblob':
					case 'mediumblob':
					case 'longblob':
						$schema[$row['Field']]['type'] = 'blob';
						break;
					case 'bit':
					case 'bool':
						$schema[$row['Field']]['type'] = 'bit';
						break;
					// not sure about these ones
					case 'year':
					case 'enum':
					case 'set':
					case 'binary':
					default:
						break;
				}
			}
			
			if (!empty($schema[$row['Field']]['type'])) {
				$schema[$row['Field']] = array_merge($this->columnTypes[$schema[$row['Field']]['type']], $schema[$row['Field']]);
			}
		}
		
		return $schema;
	}
	
	function get_last_error() {
		$this->check_connection();
		return $this->connection->error;
	}
}
?>