<?php
$SLAB_ROOT = dirname(__FILE__);
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib";

require_once("{$SLAB_ROOT}/simpletest/autorun.php");
require_once("{$SLAB_LIB}/bootstrap.php");

class Test_dispatcher_get_controller_filename extends UnitTestCase {
	function test_empty_input_throws_exception() {
		$this->expectException();
		Dispatcher::__getControllerFilename('');
	}

	function test_input_gets_correct_path() {
		global $SLAB_ROOT;
		$filename = Dispatcher::__getControllerFilename('test_controller');
		$this->assertEqual($filename, "{$SLAB_ROOT}/app/controllers/test_controller.php");
	}
}

?>