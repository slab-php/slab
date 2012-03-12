<?php
// dispatcher.php

class Dispatcher {
	function dispatch($cap = null, $data = null) {
		if (empty($cap)) $cap = $this->__get_cap($_REQUEST);
		$components = $this->__get_cap_components($cap);
		$controllerName = $components['controllerName'];
		$actionName = $components['actionName'];
		$params = $components['params'];
	}



	function __get_cap($request) {
		$cap = isset($request['slab_url']) ? $request['slab_url'] : Config::get('default_route');
		if (!isset($cap)) throw new Exception(SLAB_MESSAGE_NO_VALID_ROUTE);
		if (strpos($cap, '/') === 0) $cap = substr($cap, 1);
		return $cap;
	}

	function __get_cap_components($cap) {
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

}

?>