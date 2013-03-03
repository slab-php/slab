<?php

class RedirectResult extends ActionResult {
	var $url = null;
	
	function __construct($url) {
		$this->url = $url;
	}
	
	function render() {
		global $dispatcher;
		header('Status: 302');
		header('Location: '.$dispatcher->url($this->url));
	}
	
	function renderToString() {
		return $this->url;
	}
};

?>