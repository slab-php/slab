<?php

class ActionTests extends Controller {
	function text_action_hello_world() {
		$this->text('hello, world!');
	}
}

?>