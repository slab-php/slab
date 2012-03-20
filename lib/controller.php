<?php

class Controller extends Object {
	var $methods = array();
	
	function Controller() {
		$childMethods = get_class_methods($this);
		foreach ($childMethods as $key => $value) {
			$childMethods[$key] = strtolower($value);
		}
		$parentMethods = get_class_methods('Controller');
		foreach ($parentMethods as $key => $value) {
			$parentMethods[$key] = strtolower($value);
		}
		$this->methods = array_diff($childMethods, $parentMethods);
	}
};

?>