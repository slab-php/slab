<?php

class Controller extends Object {
	var $actionName = '';		// the name of the current action, this is set by the dispatcher
	var $params = array();		// the parameters passed to the action from the /c/a/p url, this is set by the dispatcher
	
	var $methods = array();			// methods in the controller, used to check actions in Dispatcher. Set in Controller::__construct().
	var $componentRefs = array();	// references to all components, set in Dispatcher::loadController()
	
	var $data = array();		// the incoming request values (combines $_REQUEST and $_FILES into one fruity cocktail)
	
	var $view = null;			// instance of a view
	
	var $actionResult = null;
	var $dispatcher = null;
	
	function __construct($dispatcher) {
		$this->dispatcher = $dispatcher;
		$this->view = new View($this);
	
		// get the methods used in the controller
		$childMethods = get_class_methods($this);
		foreach ($childMethods as $key => $value) {
			$childMethods[$key] = strtolower($value);
		}
		$parentMethods = get_class_methods('Controller');
		foreach ($parentMethods as $key => $value) {
			$parentMethods[$key] = strtolower($value);
		}
		$this->methods = array_diff($childMethods, $parentMethods);
	}
	
	function before_action() {}
	function after_action() {}
	function before_filter() {}
	function after_filter() {}
	function init() {}
	function shutdown() {}
	
	function url($u) {
		return Dispatcher::url($u);
	}
	
	
	// set $this->view->data
	function set($key, $value = null) {
		if (!is_array($key)) $key = array($key => $value);

		foreach ($key as $k => $v) {
			$this->view->data[$k] = $v;
		}
	}
	
	function set_layout($layout) {
		$this->view->set_layout($layout);
	}
	
	function set_view($view = null, $layout = null) {
		if (isset($view)) {
			$this->view->set_view($view);
		}
		if (isset($layout)) {
			$this->view->setLayout($layout);
		}
		$this->actionResult = new ViewResult($this->view);
	}

	function partial($view = null) {
		if (isset($view)) $this->view->setView($view);
		$this->actionResult = new PartialResult($this->view);
	}

	function redirect($url) {
		$this->actionResult = new RedirectResult($url);
	}

	function redirect_refresh($u) {
		$this->actionResult = new RedirectRefreshResult($u);
	}

	function text($s) {
		$this->actionResult = new TextResult($s);
	}	

	function json($o) {
		$this->actionResult = new JsonResult($o);
	}

	// file() is a synonym for file_inline()
	function file($filename, $data, $encoding='binary') { $this->file_inline($filename, $data, $encoding); }	
	
	function file_inline($filename, $data, $encoding='binary') {
		$this->actionResult = new FileResult($filename, $data, $encoding, 'inline');
	}

	function file_attachment($filename, $data, $encoding='binary') {
		$this->actionResult = new FileResult($filename, $data, $encoding, 'attachment');
	}

	function ajax($statusCode, $data = null) {
		$this->actionResult = new AjaxResult($statusCode, $data, $this->dispatcher);
	}

	function ajax_success($data = null) {
		$this->actionResult = new AjaxResult(200, $data, $this->dispatcher);
	}

	function ajax_error($data = null) { $this->ajax_failure($data); }

	function ajax_failure($data = null) {
		$this->actionResult = new AjaxResult(500, $data, $this->dispatcher);
	}

	function file_note_found() {
		$this->actionResult = new AjaxResult(404, null, $this->dispatcher);
	}

	// excute another action and use the result of that action for this action (nested dispatch)
	function action($cap, $data = null) {
		$this->actionResult = $this->dispatcher->dispatch($cap, $data);
	}

	function object_result($obj) {
		$this->actionResult = new ObjectResult($obj);
	}
	function controller_result($controller) {
		$this->actionResult = new ControllerResult($controller);
	}
	function physical_file($filename) {
		return $this->fileInline($filename, $this->file->read($filename));
	}
	
	// This should only be used outside of a controller action as it is a dirty way of redirecting.
	// It dies after setting the header so cookies won't be saved etc
	// Preferred method is to "return redirect('url')" inside the action.
	function redirect_immediate($u) {
		header('Status: 200');
		header('Location: '.$this->dispatcher->url($u));
		die();
	}
};

?>