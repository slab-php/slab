<?php
// config.php

class Config {
	static $config = array();

	function clear() { self::$config = array(); }
	function set($k, $v) { self::$config[$k] = $v; }
	function get($k) {
		if (isset(self::$config[$k])) return self::$config[$k]; 
		return null;
	}
}

?>