<?php
define('ROOT', dirname(__FILE__));

require_once(ROOT . '/simpletest/autorun.php');
require_once(ROOT . '/../lib/global_functions.php');

class Test_global_functions extends UnitTestCase {
	function test_str_contains() {		
		$this->assertTrue(str_contains('abcdef', 'ab'));
		$this->assertTrue(str_contains('abcdef', 'ef'));
		$this->assertTrue(str_contains('abcdef', 'cd'));
		$this->assertFalse(str_contains('abcdef', 'cD'));
		$this->assertFalse(str_contains('abcdef', 'CD'));
	}
	function test_str_starts_with() {
		$this->assertTrue(str_starts_with('abcdef', 'ab'));
		$this->assertFalse(str_starts_with('abcdef', 'bc'));
	}
}

?>