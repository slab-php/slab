<?php
$SLAB_ROOT = dirname(__FILE__);
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib";

require_once("{$SLAB_ROOT}/simpletest/autorun.php");
require_once("{$SLAB_LIB}/bootstrap.php");

class Test_controller extends UnitTestCase {
	function test_view_renderer_is_valid() {
		$controller = new Controller();
		$this->assertNotNull($controller->viewRenderer);
		$this->assertIsA($controller->viewRenderer, 'ViewRenderer');
		$this->assertEqual($controller->viewRenderer->controller, $controller);
	}
}

?>