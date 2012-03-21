<?php

class ViewRenderer extends Object {
	var $controller = null;
	var $viewName = '';

	function ViewRenderer($controller) {
		$this->controller = $controller;
	}
}

?>