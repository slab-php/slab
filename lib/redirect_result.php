<?php
/* RedirectResult
** A kind of ActionResult that redirects to the provided url
** (CC A-SA) 2009 Belfry Images [http://www.belfryimages.com.au | ben@belfryimages.com.au]
*/

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