<?php
$SLAB_ROOT = dirname(__FILE__);
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib";

require_once("{$SLAB_ROOT}/simpletest/autorun.php");
require_once("{$SLAB_LIB}/bootstrap.php");

class Test_dispatcher_url extends UnitTestCase {
	function setUp() {
		Dispatcher::setUrlRewriting(true);
	}

	function test_normal_c_a_p() {
		$url = Dispatcher::url('/c/a/p');
		$this->assertEqual($url, 'tests/c/a/p');
	}
	function test_physical_file() {
		$url = Dispatcher::url('/readme.md');
		$this->assertEqual($url, 'tests/readme.md');
	}
	function test_c_a_p_without_url_rewriting() {
		Dispatcher::setUrlRewriting(false);
		$url = Dispatcher::url('/c/a/p');
		$this->assertEqual($url, 'tests/all_tests.php?slab_url=c/a/p');
	}
	function test_physical_file_without_url_rewriting() {
		Dispatcher::setUrlRewriting(false);
		$url = Dispatcher::url('/readme.md');
		$this->assertEqual($url, 'tests/readme.md');
	}
}

?>