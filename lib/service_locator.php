<?php
// service_locator.php

class ServiceLocator {
	static function get_dispatcher() { return new Dispatcher(); }
}
?>