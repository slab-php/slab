<?php
// dispatcher.php

class Dispatcher {
	static $defaultRoute = null;
	static $urlRewriting = true;
	static $inDispatch = false;

	// Configuration:
	static function setDefaultRoute($defaultRoute) {
		self::$defaultRoute = $defaultRoute;
	}
	static function setUrlRewriting($urlRewriting) {
		self::$urlRewriting = $urlRewriting;
	}



	static function dispatch($cap = null, $data = null) {
		if (self::$inDispatch) {
			$controller = Dispatcher::__innerDispatch($cap, $data);
			return $controller->actionResult;
		}

		self::$inDispatch = true;
		$controller = Dispatcher::__innerDispatch($cap, $data);
		self::$inDispatch = false;

		return $controller->actionResult;
	}

	static function __innerDispatch($cap = null, $data = null) {
		if (empty($cap)) $cap = Dispatcher::__getCap($_REQUEST);
		$components = Dispatcher::__getCapComponents($cap);
		$controllerName = $components['controllerName'];
		$actionName = $components['actionName'];
		$params = $components['params'];

		$controller =& Dispatcher::__loadController($controllerName, $actionName, $params, $data);
		if (!is_object($controller)) throw new Exception('Error loading controller');

		$controller->beforeAction();
		try {
			$controller->callMethod($actionName, $params);
			if (empty($controller->actionResult)) $controller->view();
		} catch (Exception $ex) {
			throw $ex;	// should set an ajaxError
		}
		$controller->afterAction();

		return $controller;
	}

	static function url($cap) {
		global $SLAB_ROOT;

		$root = dirname(env('PHP_SELF'));
		if ($root == '/') $root = '';

		if (file_exists(dirname($SLAB_ROOT).$cap)) return $root.$cap;

		$url = null;
		if (self::$urlRewriting) $url = $root.$cap;
		else $url = env('PHP_SELF').'?slab_url='.str_replace('?', '&amp;', substr($cap, 1, strlen($cap)-1));

		return $url;
	}



	static function &__loadController($controllerName, $actionName, $params, $data = null) {
		$className = Inflector::camelize($controllerName);
		$filename = Dispatcher::__getControllerFilename($controllerName);

		if (!file_exists($filename)) {
			$msg = <<<EOF
The <em>{$className}</em> controller could not be found at <code>{$filename}</code>
EOF;
			throw new Exception($msg);
		}

		require_once($filename);

		if (!class_exists($className)) {
			$msg = <<<EOF
The <em>{$className}</em> controller could not be loaded. Make
sure the <em>{$className}</em> controller is defined at 
<code>{$filename}</code>.
EOF;
			throw new Exception($msg);
		}

		$controller = new $className();

		Dispatcher::__checkAction($controller, $actionName);

		$controller->data = Dispatcher::__getIncomingData($data);
		$controller->viewRenderer->viewName = "{$controllerName}/{$actionName}";

		return $controller;
	}	

	static function __getCap($request) {
		$cap = null;
		
		if (isset($request['slab_url'])) $cap = $request['slab_url'];
		else $cap = Dispatcher::$defaultRoute;

		if (!isset($cap)) throw new Exception("No valid route was found. Make sure that the default_route setting is properly configured");
		
		if (strpos($cap, '/') === 0) $cap = substr($cap, 1);
		
		return $cap;
	}

	static function __getCapComponents($cap) {
		$controllerName = '';
		$actionName = '';
		$params = array();

		$parts = explode('/', $cap);
		if (count($parts) >= 1) $controllerName = lowercase($parts[0]);
		if (count($parts) >= 2) $actionName = lowercase($parts[1]);
		if (empty($actionName)) $actionName = 'index';
		if (count($parts) >= 3) $params = array_slice($parts, 2);

		return array(
			'controllerName' => $controllerName,
			'actionName' => $actionName,
			'params' => $params
		);
	}

	static function __getControllerFilename($controllerName) {		
		global $SLAB_APP_CONTROLLERS;

		if ($controllerName == '') throw new Exception('Controller name must not be empty');

		return "{$SLAB_APP_CONTROLLERS}/{$controllerName}.php";
	}

	static function __checkAction(&$controller, $actionName) {
		$actionIsReserved = $actionName == 'beforeAction' || $actionName == 'afterAction' || $actionName == 'url' ||
			$actionName == 'render' || $actionName == 'renderPartial' || $actionName == 'redirect' ||
			$actionName == 'set' || $actionName == 'text' || $actionName == 'json' ||
			$actionName == 'renderFileInline' || $actionName == 'renderFileAttachment' || $actionName == 'renderFile' ||
			$actionName == 'ajaxSuccess' || $actionName == 'ajaxFailure';
		if ($actionIsReserved) throw new Exception("Reservered action names are not permitted");

		if (strpos($actionName, '_', 0) === 0) throw new Exception("Protected and private actions are not permitted");

		$methods = array_flip($controller->methods);
		if (!isset($methods[$actionName])) {
			$controllerClass = get_class($controller);
			throw new Exception("The <em>{$actionName}</em> action could not be found in <em>{$controllerClass}</em>");
		}
	}

	static function __getIncomingData($actionData = null) {
		$data = array();

		if (!empty($actionData)) $data = array_merge($data, $actionData);

		$data = array_merge($data, $_REQUEST);
		if (isset($data['data'])) {
			$data = array_merge($data, $data['data']);
			unset($data['data']);
		}

		if (isset($_FILES['data'])) {
			$_FILES = array_merge($_FILES, $_FILES['data']);
			unset($_FILES['data']);
		}
		if (empty($_FILES['tmp_name'])) $data = array_merge($data, $_FILES);
		else {
			foreach ($_FILES as $el => $models) {
				foreach ($models as $modelName => $elArr) {
					if (!is_array($elArr)) $data[$modelName][$el] = $elArr;
					else {
						foreach ($elArr as $fieldName => $val) {
							$data[$modelName][$fieldName][$el] = $val;
						}
					} 
				}
			}
		}

		return $data;
	}
}

?>