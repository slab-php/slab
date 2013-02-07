<?php

error_reporting(E_ALL);

// Application
Config::set('app.default_route', '/test/index');
Config::set('app.default_components', array());	// don't load any components
Config::set('app.load_model_schemas', true);
Config::set('app.url_rewriting', true);

// Database
if ($_SERVER['SERVER_NAME'] == 'localhost') {
	// development settings
	Config::set('db.driver', 'mysql');
	Config::set('db.host', 'localhost');
	Config::set('db.login', 'root');
	Config::set('db.password', '');
	Config::set('db.database', '');
	Config::set('db.tablePrefix', '');
} else if ($_SERVER['SERVER_NAME'] == 'staging.server.com') {
	// staging server settings
	Config::set('db.driver', 'mysql');
	Config::set('db.host', '');
	Config::set('db.port', '');
	Config::set('db.login', '');
	Config::set('db.password', '');
	Config::set('db.database', '');
	Config::set('db.tablePrefix', '');
} else {
	// live settings
	Config::set('db.driver', 'mysql');
	Config::set('db.host', '');
	Config::set('db.port', '');
	Config::set('db.login', '');
	Config::set('db.password', '');
	Config::set('db.database', '');
	Config::set('db.tablePrefix', '');
}

// Debugging/logging
Config::set('debug.show_execution_time', true);


?>