<?php
error_reporting(E_ALL);

define('SLAB_ROOT', __FILE__);
define('SLAB_APP', dirname(SLAB_ROOT).'/app');
// This allows having a development path for the slab library:
if ($_SERVER['SERVER_NAME'] == 'localhost') {
	define('SLAB_LIB', dirname(SLAB_ROOT).'/../../lib');
} else {
	define('SLAB_LIB', dirname(SLAB_ROOT).'/lib');
}

require_once(SLAB_LIB.'/bootstrap.php');
require_once(SLAB_APP.'/config.php');

$pageLogger = new PageLogger();
$pageLogger->log('slab', 'slab', 'init');
$dispatcher = new Dispatcher($config, $pageLogger);

try {
	$actionResult = $dispatcher->dispatch();
	$actionResult->render();	
} catch (Exception $ex) {
	$dispatcher->handle_exception($ex);
}

$dispatcher->shutdown();
$pageLogger->log('slab', 'slab', 'end');
?>