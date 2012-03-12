<?php
// dispatcher.php

class Dispatcher {


	function get_cap($request) {
		return isset($request['slab_url']) ? $request['slab_url'] : Config::get('default_route');
	}

	function dispatch($cap = null, $data = null) {
		if (empty($cap)) $cap = $this->get_cap($_REQUEST);
	}

}

?>