<?php

class RedirectResult extends ActionResult {
	var $url = null;
	
	function RedirectResult($url) {
		$this->url = $url;
	}
	
	function render() {
		$actualUrl = Dispatcher::url($this->url);
		//header('Status: 302');
		header("Location: {$actualUrl}");
	}
	
	function renderToString() {
		return $this->url;
	}
};

?>