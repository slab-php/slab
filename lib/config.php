<?php

class Config extends Object {
	var $config = array();
	
	function set($k, $v) {
		$this->config[$k] = $v;
	}
	
	function get($k) {
		return $this->config[$k];
	}
};

?>