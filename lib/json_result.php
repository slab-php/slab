<?php
/* JsonResult
** A kind of ActionResult that renders the provided object as a JSON string
** (CC A-SA) 2009 Belfry Images [http://www.belfryimages.com.au | ben@belfryimages.com.au]
*/

class JsonResult extends ActionResult {
	var $obj = null;
	
	function __construct($obj) {
		$this->obj = $obj;
	}
	
	function render() {
		e($this->returnRender());
	}	
	
	function returnRender() {
		return json_encode($this->obj);
	}
};

?>