<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/slab_messages.php");
require_once("{$root}/../lib/dispatcher.php");

class Test_dispatcher_get_cap_components extends UnitTestCase {
	function test_defaults_action_to_index() {
		$dispatcher = new Dispatcher();
		$components = $dispatcher->__get_cap_components('controller');
		$this->assertEqual($components, array(
			'controllerName' => 'controller',
			'actionName' => 'index',
			'params' => ''));
	}

	function test_gets_controller_name() {
		$dispatcher = new Dispatcher();
		$components = $dispatcher->__get_cap_components('some_controller/action/param');
		$this->assertEqual($components['controllerName'], 'some_controller');
	}

	function test_gets_action_name() {
		$dispatcher = new Dispatcher();
		$components = $dispatcher->__get_cap_components('some_controller/action/param');
		$this->assertEqual($components['actionName'], 'action');
	}

	function test_gets_params() {
		$dispatcher = new Dispatcher();
		$components = $dispatcher->__get_cap_components('some_controller/action/1/2/3');
		$this->assertEqual($components['params'], array(1, 2, 3));
	}
}

?>