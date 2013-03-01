<?php
error_reporting(E_ALL);
define('SLAB_ROOT', __FILE__);
define('SLAB_APP', dirname(SLAB_ROOT).'/app');
define('SLAB_LIB', dirname(SLAB_ROOT).'/../../lib');

require_once(SLAB_LIB.'/bootstrap.php');
require_once(SLAB_APP.'/config.php');

$dispatcher = new Dispatcher($config);

$actionResult = $dispatcher->dispatch();
$actionResult->render();
$dispatcher->shutdown();
?>