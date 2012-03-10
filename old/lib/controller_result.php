<?php
class ControllerResult extends ActionResult {
	var $controller = null;
	function getController() { return $this->controller; }
	
	function __construct($controller) {
		$this->controller = $controller;
	}
	
	function render() { }	
	function renderToString() { }
};

?>