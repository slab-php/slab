<?php
class ControllerResult extends ActionResult {
	var $controller = null;
	function get_controller() { return $this->controller; }
	
	function __construct($controller) {
		$this->controller = $controller;
	}
	
	function render() { }	
	function render_to_string() { }
};

?>