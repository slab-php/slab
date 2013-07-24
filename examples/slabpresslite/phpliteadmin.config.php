<?php 
$password = 'admin';
$directory = 'app/db/';
$subdirectories = false;
$theme = 'phpliteadmin.css';
$language = 'en';
$rowsNum = 30;
$charsNum = 300;

$custom_functions = array('md5', 'md5rev', 'sha1', 'sha1rev', 'time', 'mydate', 'strtotime', 'myreplace');
function md5rev($value) {
	return strrev(md5($value));
}
function sha1rev($value) {
	return strrev(sha1($value));
}
function mydate($value) {
	return date('g:ia n/j/y', intval($value));
}
function myreplace($value){
	return preg_replace('/[^A-Za-z0-9]/', '', strval($value));	
}

$cookie_name = 'pla3412';
$debug = false;
$allowed_extensions = array('db','db3','sqlite','sqlite3');

?>