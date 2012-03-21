<?php

class Object {
	function toString() {
		return get_class($this);
	}
	
	function callMethod($method, $params = array()) {
		$paramCount = count($params);

		if ($paramCount == 0) return $this->{$method}();
		else if ($paramCount == 1) return $this->{$method}($params[0]);
		else if ($paramCount == 2) return $this->{$method}($params[0], $params[1]);
		else if ($paramCount == 3) return $this->{$method}($params[0], $params[1], $params[2]);
		else if ($paramCount == 4) return $this->{$method}($params[0], $params[1], $params[2], $params[3]);
		else if ($paramCount == 5) return $this->{$method}($params[0], $params[1], $params[2], $params[3], $params[4]);

		return call_user_func_array(array(&$this, $method), $params);
	}
};


?>