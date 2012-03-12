<?php
error_reporting(E_ALL);

define('SLAB_ROOT', dirname(__FILE__));
define('SLAB_APP', SLAB_ROOT.'/app');
define('SLAB_LIB', SLAB_ROOT.'/../lib');

require_once(SLAB_LIB.'/bootstrap.php');

$dispatcher = ServiceLocator::get_dispatcher();
$actionResult = $dispatcher->dispatch();
$actionResult->render();
?>