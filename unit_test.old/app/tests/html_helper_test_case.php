<?php
// Tests for the HTML helper

class HtmlHelperTestCase extends UnitTestCase {
	var $html;

	function setup() {
		$this->html =& Dispatcher::loadHelper('html');
	}
	function teardown() {
	}


	function testUrlIsWorking() {
		// This tests that the /css/page.css (the style sheet for this site) gets redirected to somewhere sane
		$url = $this->html->url('/css/page.css');
		
		$this->assert($url == dirname(env('PHP_SELF')).'/css/page.css');
	}
};

?>