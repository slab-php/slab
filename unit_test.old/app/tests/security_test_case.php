<?php
// Tests for the Security class

class SecurityTestCase extends UnitTestCase {
	function setup() {
	}
	function teardown() {
	}
	
	
	function testDefaultEncodeDecode() {
		$input = "testDefaultEncodeDecode";
		$encoded = Security::encode($input);
		$output = Security::decode($encoded);
		
		$this->assert($input === $output);
	}
	
	function testHashIsHashed() {
		$input = "string to hash";
		$output = Security::hash($input);
		
		$this->assert($input != $output);
	}
	
	function testHashIsUnique() {
		$h1 = Security::hash('hash1');
		$h2 = Security::hash('hash2');
		
		$this->assert($h1 != $h2);
	}
	
	function testHashIsOneWay() {
		$input = 'string to hash';
		$pass1 = Security::hash($input);
		$pass2 = Security::hash($pass1);
		
		$this->assert($input != $pass2);
	}
};

?>