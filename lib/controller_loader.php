<?php

class ControllerLoader {
	var $config;
	var $componentLoader;
	var $helperLoader;
	var $dispatcher;

	function __construct($config, $dispatcher) {
		$this->config = $config;
		$this->dispatcher = $dispatcher;

		$this->componentLoader = new ComponentLoader($dispatcher, $config);
		$this->helperLoader = new HelperLoader($dispatcher, $config);
	}

	function __get_controller_filename($controllerName, $className) {
		$appFilename = SLAB_APP."/controllers/{$controllerName}_controller.php";

		if (file_exists($appFilename)) return $appFilename;

		$appPluginFilename = SLAB_APP."/plugins/{$controllerName}/controllers/{$controllerName}_controller.php";
		if (file_exists($appPluginFilename)) return $appPluginFilename;

		$slabPluginFilename = SLAB_LIB."/plugins/{$controllerName}/controllers/{$controllerName}_controller.php";
		if (file_exists($slabPluginFilename)) return $slabPluginFilename;

		throw new Exception("The {$className} controller could not be found at {$appFilename}");
	}

	function &load_controller($controllerName, $actionName, $params, $data = null) {
		$inflector = new Inflector();

		$className = $inflector->camelize($controllerName).'Controller';

		$filename = $this->__get_controller_filename($controllerName, $className);
		require_once($filename);

		if (!class_exists($className)) {
			throw new Exception("The {$className} controller could not be loaded. Make sure the {$className} controller is defined at {$filename}");
		}
		
		$controller = new $className($this->dispatcher, $controllerName);
		$this->__check_action($controller, $actionName);
		
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
			$component =& $this->componentLoader->load_component($componentName);
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
			$helper =& $this->helperLoader->load_helper($helperName);
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

	function __check_action(&$controller, $actionName) {
		if (
			$actionName == 'before_action' || $actionName == 'after_action' || 
			$actionName == 'before_filter' || $actionName == 'after_filter' || 
			$actionName == 'url'
			) {
			throw new Exception('Reserved action names are not permitted');
		}
	
		// if the action starts with a double underscore, a protected method is being attempted, which is not allowed
		// Single underscores are allowed, used as a convention for partials
		if (strpos($actionName, '__', 0) === 0) {
			throw new Exception('Protected actions are not permitted');
		}
		
		// make sure the method exists
		$methods = array_flip($controller->methods);
		if (!isset($methods[$actionName])) {
			$controllerClass = get_class($controller);
			throw new Exception("The {$actionName} action could not be found in the {$controllerClass} controller");
		}
	}

}

?>