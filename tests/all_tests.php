<?php
require_once(dirname(__FILE__).'/simpletest/autorun.php');

class AllTests extends TestSuite {
	function __construct(){
		parent::__construct();
		
		$path = dirname(__FILE__);

		foreach (glob($path.'/*.php') as $file) {
			if (basename($file) == basename(__FILE__)) continue;

			$this->addFile($file);
		}
	}
}
?>