<?php
class ObjectResult extends ActionResult {
	var $obj = null;
	function getObject() { return $this->obj; }
	
	function __construct($obj) {
		$this->obj = $obj;
	}
	
	function render() { }	
	function renderToString() { }
};

?>