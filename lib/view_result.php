<?php

class ViewResult extends ActionResult {
	var $viewRenderer = null;

	function ViewResult($viewRenderer) {
		$this->viewRenderer = $viewRenderer;
	}

	function render() {
		e($this->renderToString());
	}
	function renderToString() { 
		$result = $this->viewRenderer->render();
		return $result;
	}
}

?>