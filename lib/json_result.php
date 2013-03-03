<?php

class JsonResult extends ActionResult {
	var $obj = null;
	
	function __construct($obj) {
		$this->obj = $obj;
	}
	
	function render() {
		$json = json_encode($this->obj);
	
		$html =& Dispatcher::loadHelper('html');
		$html->headerNoCache();
		e($json);
	}	
	
	function render_to_string() {
		$json = json_encode($this->obj);
		
		return $json;
	}
};

?>