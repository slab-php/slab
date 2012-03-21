<?php

class TextResult extends ActionResult {
	var $text = '';

	function TextResult($text) {
		$this->text = $text;
	}

	function render() {
		e($this->text);
	}
	function renderToString() { 
		return $this->text;
	}
}

?>