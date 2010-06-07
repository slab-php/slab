<?php

class TestController extends AppController {
	var $name = 'TestController';
	
	function hello_world() {
		return $this->render();
	}
	
	function test_dispatch() {
		return 'Testing dispatch: <pre>'.h(Dispatcher::dispatch('/test/hello_world')).'</pre>';
	}
	
};

?>