<?php

class RedirectRefreshResult extends ActionResult {
	var $url = null;
	
	function __construct($url) {
		$this->url = $url;
	}
	
	function render() {
		global $dispatcher;
		header('Status: 200');
		header('Refresh: 0; '.$dispatcher->url($this->url));
	}
	
	function render_to_string() {
		return $this->url;
	}
};

?>