<?php

class CookieComponent extends Component {
	var $config = null;
	var $cookieName = null;
	var $expire = 0;	// the time that the cookie will expire. eg time()+60*60*24*30 will expire in 30 days. Set to 0 for a session cookie.
	var $path = '';		// the path for the cookie is available at. Set to '/' to be available site-wide. By default it is accessible from the base url (usually '/')
	var $domain = '';
	var $secure = false;
	var $httponly = false;	// only in PHP 5.2 and above
	var $useEncryption = true;
	var $data = null;

	function __construct($config) {
		$this->config = $config;
	}
	
	function init() {
		$this->cookieName = $this->config->get('cookie.default_cookie_name');
		$this->expire = $this->config->get('cookie.expire');
		$this->path = '/';
		$this->domain = $this->config->get('cookie.domain');
		$this->secure = $this->config->get('cookie.secure');
		$this->httponly = $this->config->get('cookie.httponly');
		$this->useEncryption = $this->config->get('cookie.use_encryption');
	}
	
	function init_cookie() {
		// copy and decrypt the current cookie
		// This function is called by the dispatcher between init()ing and beforeFilter()ing the controllers
		// as the Session component requires the cookie to be loaded before its beforeFilter() methods executes.
		// Putting this in beforeFilter() (which would otherwise be the case) will silently kill sessions if
		// Session::beforeFilter() were to execute before Cookie::beforeFilter()
		$this->data = isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : array();
		if ($this->useEncryption) {
			$this->__decrypt_data();
		}
	}
	
	// $value is either an array or a string
	// Note that this method does not affect the extracted cookie values in $this->data
	// The cookie that gets is named data[$this->cookieName][$name]
	// If $value is an array, setCookie() recursively processes $value into data[$this->cookieName][$name]
	// When the cookie is returned, the value will be accessible via $this->data[$name]
	function set($name, $value = '', $prefix = null) {
		$security = new Security($this->config);

		if (empty($prefix)) {
			$prefix = $this->cookieName;
		}

		if (!is_array($value)) {
			if ($this->useEncryption) {
				$value = $security->encode($value);
			}
		
			if (version_compare(PHP_VERSION, '5.2', '>=')) {
				// using at least PHP 5.2, include the $httponly argument
				setcookie(
					$prefix.'['.$name.']', 
					$value,
					$this->expire,
					$this->path,
					$this->domain,
					$this->secure,
					$this->httponly
					);
			} else {
				setcookie(
					$prefix.'['.$name.']', 
					$value,
					$this->expire,
					$this->path,
					$this->domain,
					$this->secure
					);
			}
			return;
		}
		
		// use recursion to set arrays to the cookie. The prefix is built up, so the cookie name becomes something like data[$this->cookieName][myArray][myKey]
		foreach ($value as $key=>$val) {
			$newPrefix = $prefix.'['.$name.']';
			$this->set($key, $val, $newPrefix);
		}
	}
	
	// Note that this method does not affect the extracted cookie values in $this->data
	// It recursively removes any cookie values stored under and including data[$this->cookieName][$name]
	// If you want to be tricky and delete a nested value, do something like this:
	//		deleteCookie('', "['myCookie'][''myNestedValue']");
	function remove($name, $idx=null) {
		if (empty($idx)) {
			$idx = "['".$name."']";
		}

		if (!eval('return isset($this->data'.$idx.');')) {
			return;
		}
		$a = eval('return $this->data'.$idx.';');
		if (!is_array($a)) {
			// remove the cookie by setting the expire time well in the past
			setcookie(
				$this->cookieName.str_replace("'",'',$idx), 
				'', 
				time()-100000, 
				$this->path, 
				$this->domain);
			return;
		}
		
		foreach ($a as $k=>$v) {
			$newIdx = $idx."['".$k."']";
			$this->remove($k, $newIdx);
		}
	}
	
	function removeAll() {
		foreach ($this->data as $k=>$v) {
			$this->remove($k);
		}
	}
	
	function __decrypt_data($arr = null) {
		$security = new Security($this->config);

		if (empty($arr)) {
			$arr =& $this->data;
		}
		if (empty($arr)) {
			return null;
		}
		
		foreach ($arr as $k=>$v) {
			if (!is_array($v)) {
				$arr[$k] = $security->decrypt($v);
			} else {
				$arr[$k] = $this->__decrypt_data($v);
			}
		}
		
		return $arr;
	}
}
?>