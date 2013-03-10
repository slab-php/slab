<?php

class HelperLoader {
	var $dispatcher;
	var $config;

	function __construct($dispatcher, $config) {
		$this->dispatcher = $dispatcher;
		$this->config = $config;
	}

	function &load_helper($helperName) {
		if (!empty($this->dispatcher->helperRefs[$helperName])) {
			return $this->dispatcher->helperRefs[$helperName];
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

		$helper = new $helperClass($this->config, $this->dispatcher);
		$this->dispatcher->helperRefs[$helperName] =& $helper;

		return $helper;
	}
}

?>