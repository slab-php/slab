<?php

require_once(SLAB_APP.'/services/gallery_service.php');

class TestController extends AppController {
	var $galleryService;

	function before_filter() {
		$this->galleryService = new GalleryService($this->disaptcher);
	}

	function index() {
	}
	
	function hello_world() {
		$name = '';
		if (isset($this->data['name'])) {
			$name = $this->data['name'];
		}
		$this->set('name', $name);
	}
	
	function hello_world_via_get($colour = null) {
		if (isset($colour)) {
			$colours = array(
				'red' => 'ff0000',
				'green' => '00ff00',
				'blue' => '0000ff'
				);
			$this->set('colour', $colour);
			$this->set('hex', $colours[$colour]);
		}
	}
	
	function test_dispatch() {
		$result = $this->dispatcher->dispatch('/test/hello_world');
		$this->text('Testing dispatch: <pre>'.h($result->render_to_string()).'</pre>');
	}

	function plugin_test() {
		$this->set('items', $this->galleryService->get_items());
	}

	function markdown_example() {}
}

?>