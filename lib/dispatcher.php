<?php
// dispatcher.php

class Dispatcher {
	static function dispatch($cap = null, $data = null) {
		if (empty($cap)) $cap = Dispatcher::__get_cap($_REQUEST);
		$components = $this->__getCapComponents($cap);
		$controllerName = $components['controllerName'];
		$actionName = $components['actionName'];
		$params = $components['params'];
	}



	static function __getCap($request) {
		$cap = isset($request['slab_url']) ? $request['slab_url'] : Config::get('default_route');
		if (!isset($cap)) throw new Exception("No valid route was found. Make sure that the default_route setting is properly configured");
		if (strpos($cap, '/') === 0) $cap = substr($cap, 1);
		return $cap;
	}

	static function __getCapComponents($cap) {
		$controllerName = '';
		$actionName = '';
		$params = '';

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

	static function &__loadController($controllerName, $actionName, $params, $data = null) {
		$className = Inflector::camelize($controllerName);
		$filename = Dispatcher::__getControllerFilename($controllerName);

		if (!file_exists($filename)) {
			throw new Exception("The <em>$className</em> controller could not be found at <code>$filename</code>");
		}

		require_once($filename);

		if (!class_exists($className)) {
			throw new Exception("The <em>{$className}</em> controller could not be loaded. Make sure the <em>{$className}</em> controller is defined at <code>{$filename}</code>.");
		}

		$controller = new $className();

		return $controller;
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
}

?>