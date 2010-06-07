<?php
// Slab unit tests

class TestsController extends UnitTestSuite {
	function __construct() {
		$this->addTest('html_helper');
		$this->addTest('controller');
		$this->addTest('security');
		parent::__construct();
	}
}

?>