<?php

class FileResult extends ActionResult {
	var $filename;
	var $data;
	var $encoding;
	var $contentDisposition;
	
	function __construct($filename, $data, $encoding, $contentDisposition) {
		$this->filename = $filename;
		$this->data = $data;
		$this->encoding = $encoding;
		$this->contentDisposition = $contentDisposition;
	}
	
	function render() {
		$mimeType = get_mime_type_from_filename($this->filename);

		// Generate the headers
		header('Content-Type: '.$mimeType);
		header('Content-Length: '.strlen($this->data));
		header('Content-Disposition: '.$this->contentDisposition.'; filename='.$this->filename);
		header('Content-Transfer-Encoding: '.$this->encoding);

		e($this->data);
	}
	
	function render_to_string() {
		return $this->data;
	}
};

?>