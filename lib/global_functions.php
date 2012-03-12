<?php
// global_functions.php
// Some of these were 'inspired' by CakePHP (https://github.com/cakephp/cakephp/blob/master/lib/Cake/basics.php)
// such as the names of some functions (e() and pr() for example)

function e($s) { echo($s); }
function h($s) { return htmlspecialchars($s); }
function eh($s) { e(h($s)); }

function lowercase($s) { return strtolower($s); }
function to_lower($s) { return strtolower($s); }
function lc($s) { return strtolower($s); }
function low($s) { return strtolower($s); }

function uppercase($s) { return strtoupper($s); }
function to_upper($s) { return strtoupper($s); }
function uc($s) { return strtoupper($s); }
function up($s) { return strtoupper($s); }

function pr($s) {
	e('<pre>');
	print_r($s);
	e('</pre>');
}

// Returns if a given string $source contains the specified search string $search
// If $search is an array, returns true if any of the items in $search is contained in $source
function str_contains($source, $search) {
	if (!is_array($search)) return strpos($source, $search) !== FALSE;

	foreach ($search as $s) {
		if (str_contains($source, $s)) return true;
	}
	return false;
}

	
// returns if a given string $source starts with the specified search string $search
// If $search is an array, returns true if $source starts with any of the items in $search
function str_starts_with($source, $search) {
	if (is_array($search)) {
		foreach ($search as $s) {
			if (str_starts_with($source, $s)) {
				return true;
			}
		}
		return false;
	}

	return strpos($source, $search) === 0;
}




?>