<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/slab_messages.php");
require_once("{$root}/../lib/dispatcher.php");

class Test_dispatcher_load_controller extends UnitTestCase {
	function test_fail() { $this->fail(); }
}

?>