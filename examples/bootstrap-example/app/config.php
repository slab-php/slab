<?php

$slabApplicationName = 'BootstrapExample';

$config = new Config();

$config->set('app.default_route', '/test/index');
$config->set('app.default_components', array(/*'db', */'cookie', 'session', 'file'));
$config->set('app.default_helpers', array('html', 'number'));
$config->set('app.load_model_schemas', true);
$config->set('app.url_rewriting', true);

// Database
if ($_SERVER['SERVER_NAME'] == 'localhost') {
	// development settings
	$config->set('db.driver', 'mysql');
	$config->set('db.host', 'localhost');
	$config->set('db.login', 'root');
	$config->set('db.password', '');
	$config->set('db.database', '');
	$config->set('db.tablePrefix', '');
} else if ($_SERVER['SERVER_NAME'] == 'staging.server.com') {
	// staging server settings
	$config->set('db.driver', 'mysql');
	$config->set('db.host', '');
	$config->set('db.port', '');
	$config->set('db.login', '');
	$config->set('db.password', '');
	$config->set('db.database', '');
	$config->set('db.tablePrefix', '');
} else {
	// live settings
	$config->set('db.driver', 'mysql');
	$config->set('db.host', '');
	$config->set('db.port', '');
	$config->set('db.login', '');
	$config->set('db.password', '');
	$config->set('db.database', '');
	$config->set('db.tablePrefix', '');
}

// Debugging/logging
$config->set('debug.show_execution_time', true);

// Security
$config->set('security.encryption_key', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3');
// This should be set to true for better encryption/decryption, but I'm having trouble with loading the mcrypt module
$config->set('security.use_mcrypt', false);

// Cookies
$config->set('cookie.default_cookie_name', "{$slabApplicationName}_data");
$config->set('cookie.expire', 0);
$config->set('cookie.domain', false);
$config->set('cookie.secure', false);
$config->set('cookie.httponly', false);
$config->set('cookie.use_encryption', true);

// Sessions
$config->set('session.cookie_name', "{$slabApplicationName}_session");
$config->set('session.timeout', 60*60);
$config->set('session.type', 'file');	// 'cookie'
$config->set('session.id_type', 'cookie');
$config->set('session.database_table', 'slab_sessions');
$config->set('session.filename_prefix', 'session_');


?>