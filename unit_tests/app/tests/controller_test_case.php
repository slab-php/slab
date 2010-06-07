<?php
// Tests for the Controller class and mechanism

class ControllerTestCase extends UnitTestCase {
	function setup() {
	}
	function teardown() {
	}
	
	
	function testControllerConstructs() {
		$actionResult = Dispatcher::dispatch('/controller_test/action_test');
		eval($actionResult->s);
		$this->assert($executedConstruct === true);
	}

	function testControllerExecutesBeforeAction() {
		$actionResult = Dispatcher::dispatch('/controller_test/action_test');
		eval($actionResult->s);
		$this->assert($executedBeforeAction === true);
	}

	function testControllerExecutesAfterAction() {
		$actionResult = Dispatcher::dispatch('/controller_test/action_test');
		eval($actionResult->s);
		$this->assert($executedAfterAction === true);
	}

	function testControllerExecutesAction() {
		$actionResult = Dispatcher::dispatch('/controller_test/action_test');
		eval($actionResult->s);
		$this->assert($executedAction === true);
	}
};

?>