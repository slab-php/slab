<?php
/* Some parts are from CodeIgniter */

class HtmlHelper extends Helper {
	var $config = null;
	var $dispatcher = null;

	function __construct($config, $dispatcher) {
		$this->config = $config;
		$this->dispatcher = $dispatcher;
	}
	
	function label($forId, $value) {
		$value = h($value);
		
		$r = '<label';
		if (!empty($forId)) $r .= " for='{$forId}'";		
		$r .= ">{$value}</label>";
		
		return $r;
	}	
	
	function inputHidden($params) {
		$params = array_merge(array(
			'name' => '',
			'id' => '',
			'value' => '',
		), $params);
		extract($params);
		
		return "<input type='hidden' name='{$name}' id='{$id}' value='{$value}' />";
	}
	function inputText($params) {
		$params = array_merge(array(
			'type' => 'text'
		), $params);
		return $this->input($params);
	}
	function inputUrl($params) {
		$params = array_merge(array(
			'type' => 'url'
		), $params);
		return $this->input($params);
	}
	function inputFile($params) {
		$params = array_merge(array(
			'type' => 'file'
		), $params);
		return $this->input($params);
	}
	function input($params) {
		$params = array_merge(array(
			'name' => '',
			'id' => '',
			'value' => '',
			'label' => null,
			'type' => 'text'
		), $params);
		extract($params);
		
		$s = '';
		
		if (isset($label)) {
			$s .= "<label for='{$id}'>{$label}</label> ";
		}
		
		$s .= "<input type='{$type}' name='{$name}' id='{$id}' value='{$value}' />";
		
		return $s;
	}
	function textarea($params) {
		$params = array_merge(array(
			'name' => '',
			'id' => '',
			'value' => '',
			'label' => null,
			'rows' => 8,
			'cols' =>  80
		), $params);
		extract($params);
		
		$s = '';
		
		if (isset($label)) {
			$s .= "<label for='{$id}'>{$label}</label> ";
		}
		
		$s .= "<textarea name='{$name}' id='{$id}' rows='{$rows}' cols='{$cols}'>{$value}</textarea>";
		
		return $s;
	}
	
	function select($params) {
		$params = array_merge(array(
			'name' => '',
			'id' => '',
			'options' => '',
			'current' => null,
			'label' => null,
		), $params);
		extract($params);
		
		$s = '';
		
		if (isset($label)) {
			$s .= "<label for='{$id}'>{$label}</label> ";
		}
		
		foreach ($options as $k => $v) {
			$s .= "<option value='{$k}'";
			if (isset($current) && $k == $current) {
				$s .= ' selected="selected" ';
			}
			$s .= ">{$v}</option>";
		}
		
		return $this->__selectWrapper($name, $id, $s);;
	}
	
	function selectIntFromRange($name, $id, $from, $to, $current) {
		$s = '';
		
		for ($i = $from; $i < $to + 1; $i ++) {
			$s .= "<option";
			if ($i == $current) $s .= ' selected="selected" ';
			$s .= ">{$i}</option>";
		}
		
		return $this->__selectWrapper($name, $id, $s);
	}
	
	function __selectWrapper($name, $id, $options) {
		return "<select name=\"{$name}\" id=\"{$id}\">{$options}</select>";
	}


	function url($u) {
		return $this->dispatcher->url($u);
	}	
	
	// This is adapted from CodeIgniter
	function headerStatus($code, $reason = null) {
		// check the code
		if ($code == '' || !is_numeric($code)) {
			throw new Exception("Status codes must be numeric - ({$code}) is invalid");
		}
		
		// get the reason
		if (empty($reason) && isset($this->headerStatusCodes[$code])) {
			$reason = $this->headerStatusCodes[$code];
		}
		if ($reason == '') {
			throw new Exception("No status text is available for status code {$code}");
		}
		
		// CGI clients don't receive the HTTP/1.X header
		if (substr(php_sapi_name(), 0, 3) == 'cgi') {
			header('Status: '.$code.' '.$reason);
			return;
		}
		
		$serverProtocol = 'HTTP/1.1';
		if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.0') {
			$serverProtocol = 'HTTP/1.0';
		}
		
		header("$serverProtocol $code $reason", true, $code);
	}

	// Write the headers to trigger no-cache for IE (used by some AJAX-y View subclasses)
	function headerNoCache() {
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Expires: 0');
	}

	
	// Copied from CodeIgniter
	var $headerStatusCodes = array(
		'200'	=> 'OK',
		'201'	=> 'Created',
		'202'	=> 'Accepted',
		'203'	=> 'Non-Authoritative Information',
		'204'	=> 'No Content',
		'205'	=> 'Reset Content',
		'206'	=> 'Partial Content',		
		'300'	=> 'Multiple Choices',
		'301'	=> 'Moved Permanently',
		'302'	=> 'Found',
		'304'	=> 'Not Modified',
		'305'	=> 'Use Proxy',
		'307'	=> 'Temporary Redirect',		
		'400'	=> 'Bad Request',
		'401'	=> 'Unauthorized',
		'403'	=> 'Forbidden',
		'404'	=> 'Not Found',
		'405'	=> 'Method Not Allowed',
		'406'	=> 'Not Acceptable',
		'407'	=> 'Proxy Authentication Required',
		'408'	=> 'Request Timeout',
		'409'	=> 'Conflict',
		'410'	=> 'Gone',
		'411'	=> 'Length Required',
		'412'	=> 'Precondition Failed',
		'413'	=> 'Request Entity Too Large',
		'414'	=> 'Request-URI Too Long',
		'415'	=> 'Unsupported Media Type',
		'416'	=> 'Requested Range Not Satisfiable',
		'417'	=> 'Expectation Failed',
		'500'	=> 'Internal Server Error',
		'501'	=> 'Not Implemented',
		'502'	=> 'Bad Gateway',
		'503'	=> 'Service Unavailable',
		'504'	=> 'Gateway Timeout',
		'505'	=> 'HTTP Version Not Supported'
	);

};

?>