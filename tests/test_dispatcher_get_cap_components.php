<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/dispatcher.php");

class Test_dispatcher_get_cap_components extends UnitTestCase {
	function test_defaults_action_to_index() {
		$components = Dispatcher::__getCapComponents('controller');
		$this->assertEqual($components, array(
			'controllerName' => 'controller',
			'actionName' => 'index',
			'params' => array()));
	}

	function test_gets_controller_name() {
		$components = Dispatcher::__getCapComponents('some_controller/action/param');
		$this->assertEqual($components['controllerName'], 'some_controller');
	}

	function test_gets_action_name() {
		$components = Dispatcher::__getCapComponents('some_controller/action/param');
		$this->assertEqual($components['actionName'], 'action');
	}

	function test_gets_params() {
		$components = Dispatcher::__getCapComponents('some_controller/action/1/2/3');
		$this->assertEqual($components['params'], array(1, 2, 3));
	}
}

?>