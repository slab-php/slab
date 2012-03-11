<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');

class AllTests extends TestSuite {
	function __construct(){
		parent::__construct();
		$this->addFile('test_template.php');
	}
}
?>