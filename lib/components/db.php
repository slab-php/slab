<?php

class DbComponent extends Component {
	var $config = null;
	var $db = null;
	var $dispatcher;

	function __construct($config, $dispatcher) {
		$this->config = $config;
		$this->dispatcher = $dispatcher;
	}

	function init() {
		$this->dispatcher->pageLogger->log('db_component', 'init', 'start');

		$this->db = null;
		$driver = $this->config->get('db.driver');
		if ($driver == 'mysql') {
			$this->dispatcher->pageLogger->log('db_copmponent', 'load_driver', 'start', 'MySql');
			$host = $this->config->get('db.host');
			$port = $this->config->get('db.port');
			$login = $this->config->get('db.login');
			$password = $this->config->get('db.password');
			$database = $this->config->get('db.database');
			$tablePrefix = $this->config->get('db.tablePrefix');
			$this->db = new MySqlDatabaseDriver($this->dispatcher, $host, $port, $login, $password, $database, $tablePrefix);
			$this->dispatcher->pageLogger->log('db_component', 'load_driver', 'end', 'MySql');
		}
		// add other database drivers here
		
		if (!empty($this->db)) {
			$this->dispatcher->pageLogger->log('db_component', 'connect', 'start');
			if (!$this->db->connect()) {
				$lastError = $this->get_last_error();
				throw new Exception("An error occured while connecting to the database: {$lastError}");
			}
			$this->dispatcher->pageLogger->log('db_component', 'connect', 'end');
		}

		$this->dispatcher->pageLogger->log('db_component', 'init', 'end');
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