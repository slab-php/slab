<?php

class ComponentLoader {
	var $dispatcher;
	var $config;

	function __construct($dispatcher, $config) {
		$this->dispatcher = $dispatcher;
		$this->config = $config;
	}

	function &load_component($componentName) {
		if (!empty($this->dispatcher->componentRefs[$componentName])) {
			return $this->dispatcher->componentRefs[$componentName];
		}
		
		$this->dispatcher->pageLogger->log('component_loader', 'load_component', 'start', "Component: {$componentName}");

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
		
		$component = new $componentClass($this->config, $this->dispatcher);
		$component->init();
		$this->dispatcher->componentRefs[$componentName] =& $component;

		$this->dispatcher->pageLogger->log('component_loader', 'load_component', 'end', "Component: {$componentName}");

		return $component;
	}
	

}

?>