<?php
$SLAB_ROOT = dirname(__FILE__);
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib";

require_once("{$SLAB_ROOT}/simpletest/autorun.php");
require_once("{$SLAB_LIB}/bootstrap.php");

class Test_dispatcher_load_controller extends UnitTestCase {
	function test_throws_exception_for_unknown_controller() {
		$this->expectException();
		Dispatcher::__loadController('none', 'none', 'none');
	}
	function test_returns_correct_controller() {
		$controller = Dispatcher::__loadController('dispatcher_load_controller_test', null, null);
		$this->assertIsA($controller, 'DispatcherLoadControllerTest');
	}
	function test_no_class_in_file_throws_exception() {
		$this->expectException();
		Dispatcher::__loadController('dispatcher_load_controller_no_class_test', 'test', null);
	}
	function test_action_not_found_throws_exception() {
		$this->expectException();
		Dispatcher::__loadController('dispatcher_load_controller_test', 'none', null);
	}
}

?>