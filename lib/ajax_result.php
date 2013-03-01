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
		$html =& $this->dispatcher->loadHelper('html');
		$html->headerStatus($this->statusCode);
		$html->headerNoCache();
		if (!empty($this->data)) {
			e($this->data);
		}
	}
	
	function renderToString() {
		return $this->data;
	}
};

?>