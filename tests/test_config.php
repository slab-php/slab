<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/config.php");

class Test_config extends UnitTestCase {
	function setUp() {
		Config::clear();
	}

	function test_setting_and_getting() {
		Config::set('test', 'value');
		$this->assertEqual(Config::get('test'), 'value');
	}

	function test_getting_without_set() {
		$this->assertNull(Config::get('no value'));
	}

	function test_clear() {
		Config::set('test', 'value');
		Config::clear();
		$this->assertNull(Config::get('test'));
	}
}

?>