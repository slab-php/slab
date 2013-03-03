<?php

class Model extends Object {
	var $tableName = null;
	var $primaryFieldName = null;
	var $schema = null;
	var $database = null;

	function __construct($database, $tableName, $primaryFieldName = 'id') {
		$this->database = $database;
		$this->tableName = $tableName;
		$this->primaryFieldName = $primaryFieldName;

		$this->schema = $this->database->get_table_schema($this->tableName);
	}	

	function get_last_error() {
		return $this->database->get_last_error();
	}
	
	function create() {
		$model = array();

		foreach ($this->schema as $col => $colSchema) {
			if (!isset($colSchema['default'])) continue;
			$model[$col] = $colSchema['default'];
		}

		return $model;
	}
	
	// Find a single model (first result) by the id (primary key)
	function find($id, $fields = null) { return $this->load($id, $fields); }
	function get($id, $fields = null) { return $this->load($id, $fields); }
	function load($id, $fields = null) {
		return $this->load_first(array($this->primaryFieldName => $id), $fields);
	}
	
	function find_all($conditions=null, $fields=null, $order=null) { return $this->load_all($conditions, $fields, $order); }
	function get_all($conditions=null, $fields=null, $order=null) { return $this->load_all($conditions, $fields, $order); }
	function load_all($conditions=null, $fields=null, $order=null) {
		return $this->database->select(
			$this->tableName,
			$fields,
			$this->__process_conditions($conditions),
			$order
		);
	}
	
	function find_all_by_query($sql) { return $this->database->query($sql); }
	function get_all_by_query($sql) { return $this->database->query($sql); }
	function load_all_by_query($sql) { return $this->database->query($sql); }
	function query($q) { return $this->database->query($sql); }
	
	// find the first model matching the conditions
	function find_first($conditions=null, $fields=null, $order=null) { return $this->load_first($conditions, $fields, $order); }
	function get_first($conditions=null, $fields=null, $order=null) { return $this->load_first($conditions, $fields, $order); }
	function load_first($conditions=null, $fields=null, $order=null) {

		$result = $this->database->select(
			$this->tableName,
			$fields,
			$this->__process_conditions($conditions),
			$order,
			null,
			'1');

		if (count($result) == 0) return null;

		return $result[0];
	}
	
	// find the first model by the given key and value, or an array of key/values in $key
	function find_by($key, $val=null) { return $this->load_by($key, $val); }
	function get_by($key, $val=null) { return $this->load_by($key, $val); }
	function load_by($key, $val=null) {
		$condition = '';
		
		if (!is_array($key)) {
			$condition = $key . '=' . $this->escape($val, $key);
		} else {
			$keys = array_keys($key);
			$vals = array_values($key);
			
			for ($i = 0; $i < count($keys); $i ++) {
				$key = $keys[$i];
				$val = $vals[$i];
				$condition .= $key . '=' . $this->escape($val, $key);
				if ($i != count($keys) - 1) {
					$condition .= ' AND ';
				}
			}
		}
	
		return $this->load_first($condition);
	}
	
	function __process_conditions($conditions) {
		if (empty($conditions) || !is_array($conditions)) {
			return $conditions;
		}
		
		$processedConditions = '';
		
		$keys = array_keys($conditions);
		$vals = array_values($conditions);
			
		for ($i = 0; $i < count($keys); $i ++) {
			$key = $keys[$i];
			$val = $vals[$i];
			$processedConditions .= $key . '=' . $this->escape($val, $key);
			if ($i != count($keys) - 1) {
				$processedConditions .= ' AND ';
			}
		}
		
		return $processedConditions;
	}
	
	// find all models matching the given key and value, or an array of key/values in $key
	function find_all_by($key, $val = null, $fields = null, $order = null) { return $this->load_all_by($key, $val, $fields, $order); }
	function get_all_by($key, $val = null, $fields = null, $order = null) { return $this->load_all_by($key, $val, $fields, $order); }
	function load_all_by($key, $val = null, $fields = null, $order = null) {
		$conditions = '';
		if (!is_array($key)) {		
			$conditions = $key . '=' . $this->escape($val, $key);
		} else {
			$conditions = $this->__process_conditions($key);
		}
		
		return $this->load_all($conditions, $fields, $order);
	}
	
	
	// Save the given data to the database.	If the data contains a primary key, performs
	// an update operation. Otherwise inserts a new instance of the model into the database. 
	// In either case $this->id is set to the ID of the model (the new ID for an insert). The
	// new ID is also returned by this function.
	function save($data) {
		if (!is_array($data)) {
			throw new Exception('Data to save must be an array');
		}
		$data = $this->escape($data);
		
		foreach ($data as $k=>$v) {
			if (!isset($this->schema[$k])) {
				unset($data[$k]);
			}
		}

		// if the data contains the primary key, this is an update, otherwise this is an insert
		if (isset($data[$this->primaryFieldName])) {
			$id = $data[$this->primaryFieldName];
			unset($data[$this->primaryFieldName]);
			$result = $this->database->update(
				$this->tableName,
				$data,
				"{$this->primaryFieldName}={$id}");
			$data[$this->primaryFieldName] = $id;
			return $result;
		} else {
			return $this->database->insert(
				$this->tableName,
				$data);
		}
	}
	
	
	function update_field($id, $fieldName, $fieldData) {
		return $this->save(array(
			$this->primaryFieldName => $id,
			$fieldName => $fieldData
		));
	}
	
	
	function remove($id) { return $this->delete($id); }
	function del($id) { return $this->delete($id); }
	function delete($id) {
		return $this->database->delete(
			$this->tableName, 
			$this->primaryFieldName.'='.$this->escape($id, $this->primaryFieldName));
	}
	
	// delete the models specified by given conditions (or all models if $conditions is not set)
	function del_all($conditions = null) { return $this->delete_all($conditions); }
	function remove_all($conditions = null) { return $this->delete_all($conditions); }
	function delete_all($conditions = null) {
		if (!empty($conditions) && is_array($conditions)) {
			$conditions = $this->__process_conditions($conditions);
		}
		return $this->database->delete($this->tableName, $conditions);
	}
	
	// returns whether models exist in the database that satisfy the given conditions
	function exists($conditions) {
		$conditions = $this->__process_conditions($conditions);
		$results = $this->loadAll($conditions);
		
		return !empty($results);
	}
	
	// returns the number of models that exist in the database that satisfy the given conditions
	function count($conditions = null) {
		$conditions = $this->__process_conditions($conditions);
		$result = $this->database->select(
			$this->tableName,
			'COUNT(*) AS row_count',
			$conditions);
		return $result[0]['row_count'];
	}
	
	
	// Escape the given data for use in a SQL statement. If the field name is provided the
	// database type of the field is used for escaping the data.
	function escape($data, $fieldName = null) {
		if (!is_array($data)) {
			return $this->_escape_field($data, $fieldName);
		}
		
		foreach ($data as $k=>$v) {
			if (!is_array($v)) {
				$data[$k] = $this->_escape_field($v, $k);
			}else {
				$data[$k] = $this->escape($data[$k]);
			}
		}
		
		return $data;
	}	
	// Some of this could probably be moved into Database
	function _escape_field($value, $fieldName = null) {
		if ($value === null) {
			return 'NULL';
		}
	
		if (isset($fieldName) && isset($this->schema[$fieldName])) {
			// Apply schema rules first:
			$schema = $this->schema[$fieldName];
			// apply the formatter method to the value
			if (isset($schema['formatter'])) {
				$formatter = $schema['formatter'];
				// handle date-type formats where the incoming value is not an integer
				if ($formatter == 'date' && !is_int($value)) {
					$value = strtotime($value);
				}
				if (isset($schema['format'])) {
					$value = $formatter($schema['format'], $value);
				} else {
					$value = $formatter($value);
				}
			}

			// if this is a string and there is a limit, use substring to apply the method now to avoid database truncation warnings
			if ($schema['type'] == 'string' && isset($schema['limit'])) {
				if ($schema['limit'] < strlen($value)) {
					$value = substr($value, 0, $schema['limit']);
				}
			}
			
			return $this->database->make_value_safe($value, $schema['type']);
		}

		return $this->database->make_value_safe($value);
	}
};

?>