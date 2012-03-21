<?php
// global_functions.php
// Some of these were inspired by or copied and modified from CakePHP 
// (https://github.com/cakephp/cakephp/blob/master/lib/Cake/basics.php)
// including the names of some functions (e() and pr() for example)

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

// Gets an environment variable, from CakePHP:
/**
* Gets an environment variable from available sources, and provides emulation
* for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
* IIS, or SCRIPT_NAME in CGI mode). Also exposes some additional custom
* environment information.
*
* @param string $key Environment variable name.
* @return string Environment variable setting.
* @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#env
*/
function env($key) {
	if ($key === 'HTTPS') {
		if (isset($_SERVER['HTTPS'])) {
			return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
		}
		return (strpos(env('SCRIPT_URI'), 'https://') === 0);
	}

	if ($key === 'SCRIPT_NAME') {
		if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
			$key = 'SCRIPT_URL';
		}
	}

	$val = null;
	if (isset($_SERVER[$key])) {
		$val = $_SERVER[$key];
	} elseif (isset($_ENV[$key])) {
		$val = $_ENV[$key];
	} elseif (getenv($key) !== false) {
		$val = getenv($key);
	}

	if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
		$addr = env('HTTP_PC_REMOTE_ADDR');
		if ($addr !== null) {
			$val = $addr;
		}
	}

	if ($val !== null) {
		return $val;
	}

	switch ($key) {
		case 'SCRIPT_FILENAME':
			if (defined('SERVER_IIS') && SERVER_IIS === true) {
				return str_replace('\\\\', '\\', env('PATH_TRANSLATED'));
			}
			break;
		case 'DOCUMENT_ROOT':
			$name = env('SCRIPT_NAME');
			$filename = env('SCRIPT_FILENAME');
			$offset = 0;
			if (!strpos($name, '.php')) {
				$offset = 4;
			}
			return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
			break;
		case 'PHP_SELF':
			return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
			break;
		case 'CGI_MODE':
			return (PHP_SAPI === 'cgi');
			break;
		case 'HTTP_BASE':
			$host = env('HTTP_HOST');
			$parts = explode('.', $host);
			$count = count($parts);

			if ($count === 1) {
				return '.' . $host;
			} elseif ($count === 2) {
				return '.' . $host;
			} elseif ($count === 3) {
				$gTLD = array(
					'aero',
					'asia',
					'biz',
					'cat',
					'com',
					'coop',
					'edu',
					'gov',
					'info',
					'int',
					'jobs',
					'mil',
					'mobi',
					'museum',
					'name',
					'net',
					'org',
					'pro',
					'tel',
					'travel',
					'xxx'
				);
				if (in_array($parts[1], $gTLD)) {
					return '.' . $host;
				}
			}
			array_shift($parts);
			return '.' . implode('.', $parts);
			break;
	}
	return null;
}




?>