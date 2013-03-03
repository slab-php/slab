<?php

class DbComponent extends Component {
	var $config = null;
	var $db = null;

	function __construct($config) {
		$this->config = $config;
	}

	function init() {
		$this->db = null;
		$driver = $this->config->get('db.driver');
		if ($driver == 'mysql') {
			require_once(SLAB_LIB.'/db_mysql.php');
			$this->db = new DbMySql();
		}
		// add other database drivers here
		
		if (!empty($this->db)) {
			$this->db->host = $this->config->get('db.host');
			$this->db->port = $this->config->get('db.port');
			$this->db->login = $this->config->get('db.login');
			$this->db->password = $this->config->get('db.password');
			$this->db->database = $this->config->get('db.database');
			$this->db->tablePrefix = $this->config->get('db.tablePrefix');

			if (!$this->db->connect()) {
				$lastError = $this->get_last_error();
				throw new Exception("An error occured while connecting to the database: {$lastError}");
			}
		}
	}
	
	// These methods are all just wrappers for $this->db methods
	function shutdown() {
		$this->db->disconnect();
	}
	
	function query($sql) {
		return $this->db->query($sql);
	}
	
	function select($table, $fields = null, $conditions = null, $orderBy = null, $groupBy = null, $top = null) {
		return $this->db->select($table, $fields, $conditions, $orderBy, $groupBy, $top);
	}
	
	function update($table, $data, $conditions) {
		return $this->db->update($table, $data, $conditions);
	}
	
	function insert($table, $data) {
		return $this->db->insert($table, $data);
	}
	
	function delete($table, $conditions) {
		return $this->db->delete($table, $conditions);
	}
	
 	function make_value_safe($data, $type = null) {
		return $this->db->make_value_safe($data, $type);
	}

	function get_table_schema($tableName) {
		return $this->db->get_table_schema($tableName);
	}
	
	function get_last_error() {
		return $this->db->get_last_error();
	}

}
?>