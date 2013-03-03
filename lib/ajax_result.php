<?php
class AjaxResult extends ActionResult {
	var $statusCode;
	var $data;
	var $dispatcher;
	
	function __construct($statusCode, $data, $dispatcher) {
		$this->statusCode = $statusCode;
		$this->data = $data;
		$this->dispatcher = $dispatcher;
	}
	
	function render() {
		$html =& $this->dispatcher->load_helper('html');
		$html->header_status($this->statusCode);
		$html->header_no_cache();
		if (!empty($this->data)) {
			e($this->data);
		}
	}
	
	function render_to_string() {
		return $this->data;
	}
};

?>