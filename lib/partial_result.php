<?php

class PartialResult extends ActionResult {
	var $view = null;
	
	function __construct($view) {
		$this->view = $view;
	}
	
	function render() {
		e($this->renderToString());
	}
	
	function render_to_string() {
		$layoutName = $this->view->layoutName;
		$this->view->layoutName = 'blank';		
		$result = $this->view->render();		
		$this->view->layoutName = $layoutName;
		
		return $result;
	}
};

?>