<?php
// bootstrap.php

$SLAB_APP_CONTROLLERS = "{$SLAB_APP}/controllers";

require_once("{$SLAB_LIB}/config.php");
require_once("{$SLAB_LIB}/global_functions.php");
require_once("{$SLAB_LIB}/mime_types.php");
require_once("{$SLAB_LIB}/inflector.php");
require_once("{$SLAB_LIB}/object.php");
require_once("{$SLAB_LIB}/controller.php");
require_once("{$SLAB_LIB}/view_renderer.php");
require_once("{$SLAB_LIB}/action_result.php");
require_once("{$SLAB_LIB}/text_result.php");
require_once("{$SLAB_LIB}/view_result.php");

require_once("{$SLAB_LIB}/dispatcher.php");
?>