<?php

class ViewResult extends ActionResult {
	var $view = null;
	
	function __construct($view) {
		$this->view = $view;
	}
	
	function render() {
		e($this->view->render());
	}
	
	function renderToString() {
		return $this->view->render();
	}
};

?>