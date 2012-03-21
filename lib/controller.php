<?php

class Controller extends Object {
	var $methods = array();
	var $data = array();
	var $viewRenderer = null;
	var $actionResult = null;

	function Controller() {
		$childMethods = get_class_methods($this);
		foreach ($childMethods as $key => $value) {
			$childMethods[$key] = strtolower($value);
		}
		$parentMethods = get_class_methods('Controller');
		foreach ($parentMethods as $key => $value) {
			$parentMethods[$key] = strtolower($value);
		}
		$this->methods = array_diff($childMethods, $parentMethods);

		$this->viewRenderer = new ViewRenderer($this);
	}

	function beforeAction() {}
	function afterAction() {}

	// Results:
	function view($view = null, $layout = null) {
		if (isset($view)) $this->viewRenderer->setView($view);
		if (isset($layout)) $this->viewRender->setLayout($layout);
		$this->actionResult = new ViewResult($this->viewRender);
	}
	function redirect($url) { $this->actionResult = new RedirectResult($url); }
	function text($text) { $this->actionResult = new TextResult($text); }
};

?>