<?php

class Dispatcher extends Object {
	var $componentRefs = array();
	var $helperRefs = array();
	var $config = null;
	var $__slug = '';
	var $controllerLoader;

	function __construct($config) {
		$this->config = $config;
		$this->controllerLoader = new ControllerLoader($config, $this);
	}

	function set_slug($slug) {
		$this->__slug = $slug;
	}
	function slug_is($slug) {
		if (is_array($slug)) {
			foreach ($slug as $s) {
				if ($this->slug_is($s)) {
					return true;
				}
			}
			return false;
		}
		return $slug === $this->__slug;
	}

	function get_filename($filename) {
		return dirname(SLAB_ROOT).$filename;
	}

	// returns a working url for the given /c/a/p triad
	// TODO: this will need to be extended for mod_rewrite support
	function url($cap) {
		$root = dirname(env('PHP_SELF'));
		if ($root == '/') {
			$root = '';
		}
	
		// if $cap is actually a request for a physical file, return the correct url (remembering $html->url() returns
		// a url relative to the path of the init file)		
		if (file_exists(dirname(SLAB_ROOT).$cap)) {
			return $root.$cap;
		}

		if ($this->config->get('app.url_rewriting')) {
			$newUrl = $root.$cap;
		} else {
			$newUrl = env('PHP_SELF').'?slab_url='.str_replace('?', '&amp;', substr($cap, 1, strlen($cap)-1));
		}
	
		// If the Session component is loaded, 
		// and the session id is persisted via the url, and there is an active
		// session, include the session id in the url:
		if (!empty($this->componentRefs['session'])
			&& $this->componentRefs['session']->sessionIDType == 'url' 
			&& $this->componentRefs['session']->inSession) {
			
			$newUrl .= '&amp;session_id=';
			
			// if possible, encrypt the session id
			if (empty($this->componentRefs['security'])) {
				$newUrl .= $this->componentRefs['security']->sessionID;
			} else {
				$newUrl .= $this->componentRefs['security']->encode($this->componentRefs['session']->sessionID);
			}
		}

		return $newUrl;
	}
	
	// url() returns a URL relative to / but absolute_url() includes the scheme and host name (like 'http://www.example.com/c/a/p')
	function absolute_url($cap) {
		return 'http://'.env('HTTP_HOST').$this->url($cap);
	}
	
	// Parses the given /c/a/p triad, finds loads and executes the appropriate controller, and returns the result of rendering the view
	// This has an optional $data param, this is an assoc array that is merged into the controller's data. This lets an action dispatch and
	// return another action like: $this->actionResult = $this->dispatcher->dispatch('/c/a/p', array('key'=>'value'));
	function dispatch($cap = null, $data = null) {
		$controller = $this->__inner_dispatch($cap, $data);
		
		if (empty($controller->actionResult)) {
			$controller->set_view();
		}

		return $controller->actionResult;
	}

	function partial($cap = null, $data = null) {
		$controller = $this->__inner_dispatch($cap, $data);
		$controller->partial();
		return $controller->actionResult->render_to_string();
	}

	function __inner_dispatch($cap, $data) {
		$controllerName = '';
		$actionName = '';
		$params = array();
		

		// If the cap triad is empty, fall back to the REQUEST url, then to the default route
		if (empty($cap)) {
			$cap = isset($_REQUEST['slab_url']) ? $_REQUEST['slab_url'] : $this->config->get('app.default_route');
		}
		if (empty($cap)) {
			e('No valid route was found. Make sure that the app.default_route setting is properly configured.');
			die();
		}

		// get rid of the preceding '/'
		if (strpos($cap, '/') === 0) {
			$cap = substr($cap, 1);
		}

		// Extract the controller name, action name, and parameters from the cap
		$cap = explode('/', $cap);
		if (count($cap) >= 1) {
			$controllerName = lowercase($cap[0]);
		}
		if (count($cap) >= 2) {
			$actionName = lowercase($cap[1]);
		}
		if (empty($actionName)) {
			$actionName = 'index';
		}
		if (count($cap) >= 3) {
			$params = array_slice($cap, 2);
		}

		// Load and create an instance of the controller
		$controller =& $this->controllerLoader->load_controller($controllerName, $actionName, $params, $data);
		if (!is_object($controller)) {
			throw new Exception('Error loading controller');
		}

		// if the Cookie component is loaded, call init_cookie() (as the cookie must be initialised before 
		// Session::before_action() is called below)
		if (isset($controller->Cookie)) {
			$controller->Cookie->init_cookie();
		}
				
		// call the components before_action and before_filter
		foreach (array_keys($controller->componentRefs) as $k) {
			$controller->componentRefs[$k]->before_action();
			$controller->componentRefs[$k]->before_filter();
		}

		try {
			$controller->before_action();
			$controller->before_filter();
			$controller->dispatch_method($actionName, $params);
			$controller->after_action();
			$controller->after_filter();
		} catch (Exception $ex) {
			$controller->ajax_error($ex->getMessage());
		}
		
		// call the components after_action and after_filter
		foreach (array_values($controller->componentRefs) as $c) {
			$c->after_action();
			$c->after_filter();
		}

		return $controller;
	}

	function shutdown() {
		foreach (array_values($this->componentRefs) as $c) {
			$c->shutdown();
		}
	}

	function &load_model($tableName, $primaryFieldName = 'id') {
		$db = $this->load_component('db');
		$model = new Model($db, $tableName, $primaryFieldName);

		return $model;
	}

	function handle_exception($ex) {
		e($this->partial('/slab_internals/show_exception', array('ex' => $ex)));
		die();
	}

	function &load_helper($helperName) {
		return $this->controllerLoader->helperLoader->load_helper($helperName);
	}

	function &load_component($componentName) {
		return $this->controllerLoader->componentLoader->load_component($componentName);
	}
};

?>