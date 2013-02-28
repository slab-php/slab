<?php

class Component extends Object {
	// The name of the component, eg 'SecurityComponent'
	var $name = null;
	
	// reference to the controller
	var $controller = null;

	
	// This gets called after dispatcher, controller, etc are passed but before beforeAction.
	// It is for initialisation that can't happen in the constructor but that doesn't rely on other components etc being set up yet
	// This is where configuration can be loaded
	function init() {
	}
	
	// These get called immediately before and after the action is executed.
	// When beforeAction() is called, all other components should be initialised and usable
	function beforeAction() {}
	function beforeFilter() {}
	function afterAction() {}
	function afterFilter() {}

	// This gets called after afterAction and afterFilter
	function shutdown() {}
}
?>