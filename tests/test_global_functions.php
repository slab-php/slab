<?php
define('ROOT', dirname(__FILE__));

require_once(ROOT . '/simpletest/autorun.php');
require_once(ROOT . '/../lib/global_functions.php');

class Test_global_functions extends UnitTestCase {
	function test_strContains() {		
		$this->assertTrue(strContains('abcdef', 'ab'));
		$this->assertTrue(strContains('abcdef', 'ef'));
		$this->assertTrue(strContains('abcdef', 'cd'));
		$this->assertFalse(strContains('abcdef', 'cD'));
		$this->assertFalse(strContains('abcdef', 'CD'));
	}
	function test_strStartsWith() {
		$this->assertTrue(strStartsWith('abcdef', 'ab'));
		$this->assertFalse(strStartsWith('abcdef', 'bc'));
	}
}

?>