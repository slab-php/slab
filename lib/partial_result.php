<?php

class PartialResult extends ActionResult {
	var $view = null;
	var $contentType = 'text/html';
	
	function __construct($view, $contentType = 'text/html') {
		$this->view = $view;
		$this->contentType = $contentType;
	}

	function render() {
		header("Content-Type: $this->contentType");
		
		e($this->render_to_string());
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