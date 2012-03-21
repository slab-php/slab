<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/dispatcher.php");

class Test_dispatcher_get_incoming_data extends UnitTestCase {
	function test_does_not_return_null() {
		$data = Dispatcher::__getIncomingData();
		$this->assertNotNull($data);
	}

	function test_returns_action_data() {
		$actionData = array('key' => 'value');
		$data = Dispatcher::__getIncomingData($actionData);
		$this->assertTrue(isset($data['key']));
		$this->assertEqual($data['key'], 'value');
	}

	function test_returns_request_data() {
		$_REQUEST['test_request'] = 'request value';
		$data = Dispatcher::__getIncomingData();
		$this->assertTrue(isset($data['test_request']));
		$this->assertEqual($data['test_request'], 'request value');
	}

	function test_unwinds_data_index_in_request() {
		$_REQUEST['data']['data_key'] = 'data value';
		$data = Dispatcher::__getIncomingData();
		$this->assertTrue(isset($data['data_key']));
		$this->assertEqual($data['data_key'], 'data value');
		$this->assertFalse(isset($data['data']));
	}
}

?>