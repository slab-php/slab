<?php

class TestController extends AppController {
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
		$result = Dispatcher::dispatch('/test/hello_world');
		return $this->text('Testing dispatch: <pre>'.h($result->returnRender()).'</pre>');
	}
	
}

?>