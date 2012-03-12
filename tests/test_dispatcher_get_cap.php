<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/slab_messages.php");
require_once("{$root}/../lib/dispatcher.php");

class Test_dispatcher_get_cap extends UnitTestCase {
	function setUp() {
		Config::clear();
	}

	function test_when_slab_url_is_provided_then_get_cap_returns_slab_url() {
		Config::set('default_route', 'default route');
		$request = array('slab_url' => 'request route');
		$dispatcher = new Dispatcher();
		$this->assertEqual($dispatcher->__get_cap($request), 'request route');
	}

	function test_when_slab_url_is_not_provided_then_returns_default_route() {
		Config::set('default_route', 'default route');
		$dispatcher = new Dispatcher();
		$this->assertEqual($dispatcher->__get_cap(array()), 'default route');
	}

	function test_when_no_route_can_be_found_then_get_cap_throws_exception() {
		$dispatcher = new Dispatcher();
		$this->expectException();
		$dispatcher->__get_cap(array());
	}

	function test_when_route_has_preceding_slash_it_is_removed() {
		$dispatcher = new Dispatcher();
		$this->assertEqual(
			$dispatcher->__get_cap(array('slab_url' => '/test/route')),
			'test/route');
	}
	
	function test_when_configured_route_has_preceding_slash_it_is_removed() {
		Config::set('default_route', '/test/route');
		$dispatcher = new Dispatcher();
		$this->assertEqual(
			$dispatcher->__get_cap(array()),
			'test/route');
	}
}

?>