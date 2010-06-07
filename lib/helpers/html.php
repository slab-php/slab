<?php
/* HtmlHelper
** Some parts are from CodeIgniter
** BJS20091004
** (CC A-SA) 2009 Belfry Images [http://www.belfryimages.com.au | ben@belfryimages.com.au]
*/

class HtmlHelper extends Helper {
	var $name = 'HtmlHelper';

	// Wrapper for Dispatcher::url()
	function url($u) {
		return Dispatcher::url($u);
	}
	
	
	// This is adapted from CodeIgniter
	function headerStatus($code, $reason = null) {
		// check the code
		if ($code == '' || !is_numeric($code)) {
			e('In HtmlHelper::headerStatus(), status codes must be numeric');
			die();
		}
		
		// get the reason
		if (empty($reason) && isset($this->headerStatusCodes[$code])) {
			$reason = $this->headerStatusCodes[$code];
		}
		if ($reason == '') {
			e('In HtmlHeader::headerStatus(), no status text is available for code '.$code);
			die();
		}
		
		// CGI doesn't get the HTTP/1.X header
		if (substr(php_sapi_name(), 0, 3) == 'cgi') {
			header('Status: '.$code.' '.$reason);
			return;
		}
		
		$serverProtocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '';
		if ($serverProtocol != 'HTTP/1.1' || $serverProtocol != 'HTTP/1.0') {
			$serverProtocol = 'HTTP/1.1';
		}
		
		header("$serverProtocol $code $reason", true, $code);
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