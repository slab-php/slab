<?php

class TextResult extends ActionResult {
	var $s = null;
	
	function __construct($s) {
		$this->s = $s;
	}
	
	function render() {
		e($this->s);
	}
	
	function render_to_string() {
		return $this->s;
	}
};

?>