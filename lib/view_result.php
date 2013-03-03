<?php

class ViewResult extends ActionResult {
	var $view = null;
	
	function __construct($view) {
		$this->view = $view;
	}
	
	function render() {
		e($this->view->render());
	}
	
	function render_to_string() {
		return $this->view->render();
	}
};

?>