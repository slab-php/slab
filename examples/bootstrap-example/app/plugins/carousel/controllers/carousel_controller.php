<?php

class CarouselController extends AppController {
	function index() {
		$this->set('items', $this->data['items']);
	}
}

?>