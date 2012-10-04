<?php
// component.php
// Base class for controller components

class Component extends Object {
	var $name = null;
	var $controller = null;
	
	// This gets called after dispatcher, controller, etc are passed but before beforeAction.
	// It is for initialisation that can't happen in the constructor but that doesn't rely on other components etc being set up yet
	// This is where configuration can be loaded
	function init() {}
	// This gets called after afterAction
	function shutdown() {}
	
	// These get called immediately before and after the action is executed.
	// When beforeAction() is called, all other components should be initialised and usable
	function beforeAction() {}
	function afterAction() {}
}
?>