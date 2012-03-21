<?php

class ActionTests extends Controller {
	function text_action_hello_world() {
		$this->text('hello, world!');
	}

	function redirect_to($url) {
		$this->redirect("/{$url}");
	}
}

?>