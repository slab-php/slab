<?php

class Lightbox2Controller extends AppController {
	function lightbox_css() {
		$this->partial(null, get_mime_type('css'));
	}
	function lightbox_js() {
		$this->partial(null, get_mime_type('js'));
	}
	function close_png() {
		$this->physical_file(SLAB_LIB.'/plugins/lightbox2/views/lightbox2/close.png');
	}
	function loading_gif() {
		$this->physical_file(SLAB_LIB.'/plugins/lightbox2/views/lightbox2/loading.gif');
	}
	function prev_png() {
		$this->physical_file(SLAB_LIB.'/plugins/lightbox2/views/lightbox2/prev.png');
	}
	function next_png() {
		$this->physical_file(SLAB_LIB.'/plugins/lightbox2/views/lightbox2/next.png');
	}
}

?>