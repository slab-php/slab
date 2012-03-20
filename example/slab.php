<?php
error_reporting(E_ALL);

$SLAB_ROOT = dirname(__FILE__));
$SLAB_APP = "{$SLAB_ROOT}/app";
$SLAB_LIB = "{$SLAB_ROOT}/../lib");

require_once("{$SLAB_LIB}/bootstrap.php");
require_once("{$SLAB_APP}/config.php");

$dispatcher = ServiceLocator::get_dispatcher();
$actionResult = $dispatcher->dispatch();
$actionResult->render();
?>