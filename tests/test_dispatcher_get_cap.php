<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/dispatcher.php");

class Test_dispatcher_get_cap extends UnitTestCase {
	function setUp() {
		Config::clear();
	}

	function test_when_slab_url_is_provided_then_get_cap_returns_slab_url() {
		Config::set('default_route', 'default route');
		$request = array('slab_url' => 'request route');
		$dispatcher = new Dispatcher();
		$this->assertEqual($dispatcher->get_cap($request), 'request route');
	}

	function test_when_slab_url_is_not_provided_then_returns_default_route() {
		Config::set('default_route', 'default route');
		$dispatcher = new Dispatcher();
		$this->assertEqual($dispatcher->get_cap(array()), 'default route');
	}
}

?>