<?php

class Dispatcher extends Object {
	var $componentRefs = array();
	var $helperRefs = array();
	var $baseUrl = '';
	var $config = null;

	function __construct($config) {
		$this->config = $config;
	}

	//static
	function getBaseUrl() {
		return $this->baseUrl;
	}
	
	function getFilename($filename) {
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
	
	// url() returns a URL absolute to /, absoluteUrl() includes the scheme and host name (like 'http://www.example.com/c/a/p')
	function absoluteUrl($cap) {
		return 'http://'.env('HTTP_HOST').$this->url($cap);
	}
	
	// Parses the given /c/a/p triad, finds loads and executes the appropriate controller, and returns the result of rendering the view
	// This has an optional $data param, this is an assoc array that is merged into the controller's data. This lets an action dispatch and
	// return another action like: $this->actionResult = Dispatcher::dispatch('/c/a/p', array('key'=>'value'));
	//static
	function dispatch($cap = null, $data = null) {
		$controllerName = '';
		$actionName = '';
		$params = array();
		
		$this->baseUrl = dirname(env('PHP_SELF'));

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
		$controller =& $this->loadController($controllerName, $actionName, $params, $data);
		if (!is_object($controller)) {
			throw new Exception('Error loading controller');
		}

		// if the Cookie component is loaded, call initCookie() (as the cookie must be initialised before 
		// Session::beforeAction() is called below)
		if (isset($controller->Cookie)) {
			$controller->Cookie->initCookie();
		}
				
		// call the components beforeAction and befireFilter
		foreach (array_keys($controller->componentRefs) as $k) {
			$controller->componentRefs[$k]->beforeAction();
			$controller->componentRefs[$k]->beforeFilter();
		}

		try {
			$controller->beforeAction();
			$controller->beforeFilter();
			$controller->dispatchMethod($actionName, $params);
			if (empty($controller->actionResult)) {
				// if the controller's actionResult isn't set, this means that the action didn't execute a view method,
				// so just default to $controller->view()
				$controller->view();
			}
			$controller->afterAction();
			$controller->afterFilter();
		} catch (Exception $ex) {
			$controller->ajaxError($ex->getMessage());
		}
		
		// call the components afterAction and afterFilter
		foreach (array_values($controller->componentRefs) as $c) {
			$c->afterAction();
			$c->afterFilter();
		}
		
		return $controller->actionResult;
	}

	function shutdown() {
		foreach (array_values($this->componentRefs) as $c) {
			$c->shutdown();
		}
	}

	function __innerDispatch($cap = null, $data = null) {
	}
		
	function &loadController($controllerName, $actionName, $params, $data = null) {
		$inflector = new Inflector();

		$className = $inflector->camelize($controllerName).'Controller';

		// try to load from the app
		$filename = SLAB_APP.'/controllers/'.$controllerName.'_controller.php';
		if (!file_exists($filename)) {
			throw new Exception("The {$className} controller could not be found at {$filename}");
		}
		
		// TODO: fall back on plugins
		
		require_once($filename);
		
		if (!class_exists($className)) {
			throw new Exception("The {$className} controller could not be loaded. Make sure the {$className} controller is defined at {$filename}");
		}
		
		$controller = new $className($this);
		$this->__checkAction($controller, $actionName);
		
		$controller->actionName = $actionName;
		$controller->params = $params;
		
		// copy $_REQUEST into $controller->data
		$controller->data = array();
		if (!empty($data)) {
			$controller->data = array_merge($controller->data, $data);
		}
		$controller->data = array_merge($controller->data, $_REQUEST);
		if (isset($controller->data['data'])) {
			$controller->data = array_merge($controller->data, $controller->data['data']);
			unset($controller->data['data']);
		}
		// merge $_FILES into $controller->data (uploaded files)
		if (isset($_FILES['data'])) {
			$_FILES = array_merge($_FILES, $_FILES['data']);
			unset($_FILES['data']);
		}
		// file inputs can _either_ be named like 'field_name' or 'data[Model][field_name]', but the two formats _cannot be mixed_ in one request
		// When data[model][field_name] foramt is used, can't just array_merge(), have to remap $_FILES (see http://au2.php.net/manual/en/features.file-upload.multiple.php#53240)
		if (!empty($_FILES['tmp_name'])) {
			// if $_FILES['tmp_name'] exists this is the data[Model][field_name] format
			foreach ($_FILES as $el=>$models) {
				foreach ($models as $modelName=>$elArr) {
					if (is_array($elArr)) {
						foreach ($elArr as $fieldName=>$val) {
							$controller->data[$modelName][$fieldName][$el] = $val;
						}
					} else {
						// actually this is data[field_name]...
						$controller->data[$modelName][$el] = $elArr;
					}
				}
			}
		} else {
			// this is the field_name format, just merge into $controller->data
			$controller->data = array_merge($controller->data, $_FILES);
		}

		// load and configure components
		foreach ($this->config->get('app.default_components') as $componentName) {
			$component =& $this->loadComponent($componentName);
			$component->controller =& $controller;
			$controller->componentRefs[$componentName] =& $component;
			// add as both $controller->ComponentName and $controller->componentName
			$componentName = $inflector->camelize($componentName);
			$controller->$componentName =& $component;
			$componentName = $inflector->camelback($inflector->underscore($componentName));
			$controller->$componentName =& $component;
		}
		
		// set up view
		$controller->view->viewName = $controllerName.'/'.$actionName;
		// load helpers into view
		foreach ($this->config->get('app.default_helpers') as $helperName) {
			$helper =& $this->loadHelper($helperName);
			// add (as HelperName and helperName) to both the view's helperRefs array and the controller
			$helperName = $inflector->camelize($helperName);
			$controller->$helperName =& $helper;
			$controller->view->helperRefs[$helperName] =& $helper;
			$helperName = $inflector->camelback($inflector->underscore($helperName));
			$controller->helperName =& $helper;
			$controller->view->helperRefs[$helperName] =& $helper;
		}

		return $controller;
	}

	function __checkAction(&$controller, $actionName) {
		if ($actionName == 'beforeAction' || $actionName == 'afterAction' || $actionName == 'url' ||
			$actionName == 'render' || $actionName == 'renderPartial' || $actionName == 'redirect' ||
			$actionName == 'set' || $actionName == 'text' || $actionName == 'json' ||
			$actionName == 'renderFileInline' || $actionName == 'renderFileAttachment' || $actionName == 'renderFile' ||
			$actionName == 'ajaxSuccess' || $actionName == 'ajaxFailure'
			) {
			throw new Exception('Reserved action names are not permitted');
		}
	
		// if the action starts with an underscore, a private/protected method is being attempted, which is not allowed
		if (strpos($actionName, '_', 0) === 0) {
			throw new Exception('Private/protected actions are not permitted - TODO better error handling ;-)');
		}
		
		// make sure the method exists
		$methods = array_flip($controller->methods);
		if (!isset($methods[$actionName])) {
			$controllerClass = get_class($controller);
			throw new Exception("The {$actionName} action could not be found in the {$controllerClass} controller");
		}
	}
	
	function &loadComponent($componentName) {
		if (!empty($this->componentRefs[$componentName])) {
			return $this->componentRefs[$componentName];
		}
		
		$componentFilename = SLAB_LIB.'/components/'.$componentName.'.php';
		if (!file_exists($componentFilename)) {
			throw new Exception("Unknown component {$componentName} at {$componentFilename}, only built-in components are now supported");
		}
		
		require_once($componentFilename);

		$inflector = new Inflector();
		$componentClass = $inflector->camelize($componentName) . 'Component';
		if (!class_exists($componentClass)) {
			throw new Exception("The {$componentClass} component does not exist in {$componentFilename}");
		}
		
		$component = new $componentClass($this->config);
		$component->init();
		$this->componentRefs[$componentName] =& $component;

		return $component;
	}
	
	function &loadHelper($helperName) {
		if (!empty($this->helperRefs[$helperName])) {
			return $this->helperRefs[$helperName];
		}

		$helperFilename = SLAB_LIB.'/helpers/'.$helperName.'.php';
		if (!file_exists($helperFilename)) {
			throw new Exception("Unknown helper {$helperName} at {$helperFilename}, only built-in helpers are now supported");
		}

		require_once($helperFilename);

		$inflector = new Inflector();
		$helperClass = $inflector->camelize($helperName) . 'Helper';
		if (!class_exists($helperClass)) {
			throw new Exception("The {$helperClass} helper does not exist in {$helperFilename}");
		}

		$helper = new $helperClass($this->config, $this);
		$this->helperRefs[$helperName] =& $helper;

		return $helper;
	}

	function &loadModel($tableName, $primaryFieldName = 'id') {
		$db = $this->loadComponent('db');
		$model = new Model($db, $tableName, $primaryFieldName);

		return $model;
	}
};

?>