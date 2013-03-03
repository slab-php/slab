<?php

require_once(SLAB_LIB.'/global_functions.php');

$SLAB_EXECUTION_TIME_START = getMicrotime();

require_once(SLAB_LIB.'/object.php');
require_once(SLAB_LIB.'/config.php');

if (!file_exists(SLAB_APP.'/config.php')) {
	throw new Exception('The application configuration file must be created at '.SLAB_APP.'/config.php');
}
require_once(SLAB_APP.'/config.php');

// include classes
require_once(SLAB_LIB.'/inflector.php');
require_once(SLAB_LIB.'/dispatcher.php');
require_once(SLAB_LIB.'/model.php');
require_once(SLAB_LIB.'/view.php');
require_once(SLAB_LIB.'/controller.php');
require_once(SLAB_LIB.'/component.php');
require_once(SLAB_LIB.'/helper.php');
require_once(SLAB_LIB.'/database.php');
require_once(SLAB_LIB.'/security.php');
require_once(SLAB_LIB.'/unit_test_case.php');
require_once(SLAB_LIB.'/unit_test_suite.php');
require_once(SLAB_LIB.'/BArray.php');
// ActionResult and subclasses
require_once(SLAB_LIB.'/action_result.php');
require_once(SLAB_LIB.'/view_result.php');
require_once(SLAB_LIB.'/partial_result.php');
require_once(SLAB_LIB.'/redirect_result.php');
require_once(SLAB_LIB.'/redirect_refresh_result.php');
require_once(SLAB_LIB.'/text_result.php');
require_once(SLAB_LIB.'/json_result.php');
require_once(SLAB_LIB.'/ajax_result.php');
require_once(SLAB_LIB.'/file_result.php');
require_once(SLAB_LIB.'/object_result.php');
require_once(SLAB_LIB.'/controller_result.php');

// attempt to load the app's AppController, otherwise fall back on the placeholder
if (file_exists(SLAB_APP.'/app_controller.php')) {
	require_once(SLAB_APP.'/app_controller.php');
} else {
	require_once(SLAB_LIB.'/app_controller.php');
}

?>