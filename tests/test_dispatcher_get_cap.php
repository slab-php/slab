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
		$this->assertEqual(Dispatcher::__getCap($request), 'request route');
	}

	function test_when_slab_url_is_not_provided_then_returns_default_route() {
		Config::set('default_route', 'default route');
		$this->assertEqual(Dispatcher::__getCap(array()), 'default route');
	}

	function test_when_no_route_can_be_found_then_get_cap_throws_exception() {
		$this->expectException();
		Dispatcher::__getCap(array());
	}

	function test_when_route_has_preceding_slash_it_is_removed() {
		$this->assertEqual(
			Dispatcher::__getCap(array('slab_url' => '/test/route')),
			'test/route');
	}
	
	function test_when_configured_route_has_preceding_slash_it_is_removed() {
		Config::set('default_route', '/test/route');
		$this->assertEqual(
			Dispatcher::__getCap(array()),
			'test/route');
	}
}

?>