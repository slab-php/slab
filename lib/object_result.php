<?php

class ObjectResult extends ActionResult {
	var $obj = null;
	function get_object() { return $this->obj; }
	
	function __construct($obj) {
		$this->obj = $obj;
	}
	
	function render() { }	
	function render_to_string() { }
};

?>