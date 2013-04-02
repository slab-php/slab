<?php

class BaseDatabaseDriver extends Object {
	var $columnTypes = array();

	function connect() {}
	function disconnect() {}
	function query($sql) {}
	function select($table, $fields = null, $conditions = null, $orderBy = null, $groupBy = null, $top = null) { }
	function update($table, $data, $conditions) { }
	function insert($table, $data) { }
	function delete($table, $conditions = null) { }
 	function make_value_safe($data, $type = null) { return null; }
	function get_table_schema($tableName) { return null; }
	function get_last_error() { return null; }
}

?>