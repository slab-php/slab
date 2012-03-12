<?php
$root = dirname(__FILE__);

require_once("{$root}/simpletest/autorun.php");
require_once("{$root}/../lib/mime_types.php");

class Test_mime_types extends UnitTestCase {
	function test_mime_type_for_extension() {
		$mimeType = get_mime_type_for_extension('asm');
		$this->assertEqual($mimeType, 'text/x-asm');
	}
	function test_mime_type_for_extension_where_there_are_multiples() {
		$mimeType = get_mime_type_for_extension('avi');
		$this->assertEqual($mimeType, 'video/avi');
	}
	function test_mime_type_for_filename(){
		$mimeType = get_mime_type_for_filename('test.htm');
		$this->assertEqual($mimeType, 'text/html');
	}
	function test_unknown_mime_type() {
		$mimeType = get_mime_type_for_extension('...');
		$this->assertEqual($mimeType, 'application/octet-stream');
	}
}

?>