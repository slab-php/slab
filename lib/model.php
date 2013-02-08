<?php

class Model extends Object {
	var $tableName = null;
	var $primaryFieldName = null;
	var $schema = null;
	var $database = null;
	var $id = null;

	function __construct($database, $tableName, $primaryFieldName = 'id') {
		$this->database = $database;
		$this->tableName = $tableName;
		$this->primaryFieldName = $primaryFieldName;
	}	

	function getLastError() {
		return $this->database->getLastError();
	}
	
	function create() {
		$model = array();
		foreach ($this->schema as $col => $colSchema) {
			if (!isset($colSchema['default'])) continue;
			$model[$col] = $colSchema['default'];
		}
		$model = array($this->modelName => $model);

		return $model;
	}
	
	// Find a single model (first result) by the id (primary key)
	function find($id = null, $fields = null) { return $this->load($id, $fields); }
	function get($id = null, $fields = null) { return $this->load($id, $fields); }
	function load($id = null, $fields = null) {
		if (empty($id)) {
			$id = $this->id;
		}
		$this->id = $id;
		
		$result = $this->database->select(
			$this->tableName,
			$fields,
			$this->primaryFieldName.'='.$this->escape($id, $this->primaryFieldName)
			);
		
		if (count($result) == 0) {
			return null;
		}
		return array($this->modelName=>$result[0]);
	}
	
	// find all models matching the conditions
	function findAll($conditions=null, $fields=null, $order=null) { return $this->loadAll($conditions, $fields, $order); }
	function getAll($conditions=null, $fields=null, $order=null) { return $this->loadAll($conditions, $fields, $order); }
	function loadAll($conditions=null, $fields=null, $order=null) {
		$results = $this->database->select(
			$this->tableName,
			$fields,
			$conditions,
			$order
			);

		$results2 = array();
		foreach ($results as $r) {
			$results2[] = array($this->modelName => $r);
		}
		
		return $results2;
	}
	
	function findAllByQuery($sql) { return $this->loadAllByQuery($sql); }
	function getAllByQuery($sql) { return $this->loadAllByQuery($sql); }
	function loadAllByQuery($sql) {
		$results = $this->database->query($sql);
		$results2 = array();
		foreach ($results as $r) {
			$results2[] = array($this->modelName => $r);
		}
		return $results2;
	}
	
	// find the first model matching the conditions
	function findFirst($conditions=null, $fields=null, $order=null) { return $this->loadFirst($conditions, $fields, $order); }
	function getFirst($conditions=null, $fields=null, $order=null) { return $this->loadFirst($conditions, $fields, $order); }
	function loadFirst($conditions=null, $fields=null, $order=null) {
		$result = $this->database->select(
			$this->tableName,
			$fields,
			$conditions,
			$order,
			null,
			'1');
		if (count($result) > 0) {
			return array($this->modelName => $result[0]);
		}
		return null;
	}
	
	// find the first model by the given key and value, or an array of key/values in $key
	function findatabasey($key, $val=null) { return $this->loadatabasey($key, $val); }
	function getBy($key, $val=null) { return $this->loadatabasey($key, $val); }
	function loadatabasey($key, $val=null) {
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
	
		return $this->loadFirst($condition);
	}
	
	function __processConditions($conditions) {
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
	function findAllBy($key, $val = null, $fields = null, $order = null) { return $this->loadAllBy($key, $val, $fields, $order); }
	function getAllBy($key, $val = null, $fields = null, $order = null) { return $this->loadAllBy($key, $val, $fields, $order); }
	function loadAllBy($key, $val = null, $fields = null, $order = null) {
		$conditions = '';
		if (!is_array($key)) {		
			$conditions = $key . '=' . $this->escape($val, $key);
		} else {
			$conditions = $this->__processConditions($key);
		}
		
		return $this->loadAll($conditions, $fields, $order);
	}
	
	
	// Save the given data to the database.	If the data contains a primary key, performs
	// an update operation. Otherwise inserts a new instance of the model into the database. 
	// In either case $this->id is set to the ID of the model (the new ID for an insert). The
	// new ID is also returned by this function.
	function save($data) {
		if (!is_array($data)) {
			throw new Exception('Data to save must be an array');
		}
	
		if (isset($data[$this->modelName])) {
			$data = $data[$this->modelName];
		}
		$data = $this->escape($data);
		
		foreach ($data as $k=>$v) {
			if (!isset($this->schema[$k])) {
				unset($data[$k]);
			}
		}

		// if the data contains the primary key, this is an update, otherwise this is an insert
		if (isset($data[$this->primaryFieldName])) {
			$this->id = $data[$this->primaryFieldName];
			unset($data[$this->primaryFieldName]);
			$result = $this->database->update(
				$this->tableName,
				$data,
				$this->primaryFieldName.'='.$this->id);
			$data[$this->primaryFieldName] = $this->id;
			return $result;
		} else {
			$this->id = $this->database->insert(
				$this->tableName,
				$data);
			return $this->id;
		}
	}
	
	
	// update a single field to the model identified by $this->id
	function updateField($fieldName, $fieldData) {
		return $this->save(array(
			$this->primaryFieldName => $this->id,
			$fieldName => $fieldData
		));
	}
	
	
	// remove(), del() and delete() are synonyms, delete the specified model
	// (or the model specified by $this->id if $id is not set)
	// The model (as in $model['Model']['id']) can be passed in instead of just the id
	function remove($id = null) { return $this->delete($id); }
	function del($id = null) { return $this->delete($id); }
	function delete($id = null) {
		if (empty($id)) {
			$id = $this->id;
		}

		// This allows the model instance itself to be passed to del()
		if (is_array($id) && isset($id[$this->modelName]) && isset($id[$this->modelName][$this->primaryFieldName])) {
			$id = $id[$this->modelName][$this->primaryFieldName];
		}
		$this->id = $id;
		return $this->database->delete($this->tableName, $this->primaryFieldName.'='.$this->escape($this->id, $this->primaryFieldName));
	}
	
	// removeAll(), delAll() and deleteAll() are synonyms, delete the models specified
	// given conditions (or all models if $conditions is not set)
	function delAll($conditions = null) { return $this->deleteAll($conditions); }
	function removeAll($conditions = null) { return $this->deleteAll($conditions); }
	function deleteAll($conditions = null) {
		if (!empty($conditions) && is_array($conditions)) {
			$conditions = $this->__processConditions($conditions);
		}
		return $this->database->delete($this->tableName, $conditions);
	}
	
	// returns true if the model specified by the given id or if models exist in the
	// database that satisfy the given conditions, false otherwise
	function exists($id = null, $conditions = null) {
		$results = null;
		
		if (!empty($conditions)) {
			$conditions = $this->__processConditions($conditions);
			$results = $this->loadAll($conditions);
		}
		else {
			if (empty($id)) {
				$id = $this->id;
			}
			$this->id = $id;
			$results = $this->load($this->escape($id, $this->primaryFieldName));
		}
		
		return !empty($results);
	}
	
	// returns the number of models that exist in the database that satisfy the
	// given conditions
	function count($conditions = null) {
		$conditions = $this->__processConditions($conditions);
		$result = $this->database->select(
			$this->tableName,
			'COUNT(*) AS row_count',
			$conditions);
		return $result[0]['row_count'];
	}
	
	// Just calls $this->database->query()
	function query($q) { 
		return $this->database->query($q);
	}
	
	// Escape the given data for use in a SQL statement. If the field name is provided the
	// database type of the field is used for escaping the data.
	function escape($data, $fieldName = null) {
		if (!is_array($data)) {
			return $this->_escapeField($data, $fieldName);
		}
		
		foreach ($data as $k=>$v) {
			if (!is_array($v)) {
				$data[$k] = $this->_escapeField($v, $k);
			}else {
				$data[$k] = $this->escape($data[$k]);
			}
		}
		
		return $data;
	}	
	// Some of this could probably be moved into Database
	function _escapeField($value, $fieldName = null) {
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
			
			return $this->database->makeValueSafe($value, $schema['type']);
		}

		return $this->database->makeValueSafe($value);
	}
	
	// Load the schema from the database
	function loadSchema() {
		$this->schema = $this->database->getTableSchema($this->tableName);
	}
};

?>