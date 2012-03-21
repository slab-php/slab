<?php
$SLAB_ROOT = dirname(__FILE__);
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib";

require_once("{$SLAB_ROOT}/simpletest/autorun.php");
require_once("{$SLAB_LIB}/bootstrap.php");

class Test_dispatcher_dispatch extends UnitTestCase {
	function test_returns_action_result() {
		$result = Dispatcher::dispatch('dispatcher_dispatch_test/text_action', null);
		$this->assertIsA($result, 'TextResult');
	}
}

?>