<?php
$SLAB_ROOT = dirname(__FILE__);
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib";

require_once("{$SLAB_ROOT}/simpletest/autorun.php");
require_once("{$SLAB_LIB}/bootstrap.php");

class Test_dispatcher_check_action extends UnitTestCase {
	function test_throws_exception_for_reserved_action() {
		$this->expectException();

		$controller = new TestController();

		Dispatcher::__checkAction($controller, 'set');
	}

	function test_throws_exception_for_protected_actions() {
		$this->expectException();

		$controller = new TestController();

		Dispatcher::__checkAction($controller, '_protected_action');
	}

	function test_throws_exception_for_missing_action() {
		$this->expectException();

		$controller = new TestController();

		Dispatcher::__checkAction($controller, 'missing_action');
	}

	function test_no_error_for_valid_action() {
		$controller = new TestController();

		Dispatcher::__checkAction($controller, 'valid_action');
	}
}

class TestController extends Controller {
	function _protected_action() { return ''; }
	function valid_action() { return ''; }
}

?>