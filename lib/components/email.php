<?php

/*
Example:
$this->Email->send(Array(
	'to' => 'to@domain.com',
	'from' => 'from@domain.com',
	'subject' => 'Email from Belfry Slab',
	'content' => 'Content',
	'attachments' => array(
		'filename' => 0xDATA,
		...
	)
));
If 'from' is not provided, 'to' is used as the from address.
*/

class EmailComponent extends Component {
	var $config = null;

	function __construct($config) {
		$this->config = $config;
	}

	function init() {}

	function send($settings) {
		extract($settings);
		if (!isset($to)) throw new Exception('To address was not provided');
		if (!isset($subject)) $subject = '';
		if (!isset($content)) $content = '';
		if (!isset($from)) $from = $to;
		if (!isset($attachments)) $attachments = array();

		if (!$this->__check_header_injection($content)) throw new Exception('Content contains an illegal email header');
		if (!$this->__check_referer()) throw new Exception('Referer is invalid');
		
		$boundary = md5(uniqid(time()));
		
		$headers = 
			"From: {$from}".PHP_EOL.
			"Return-Path: {$from}".PHP_EOL.
			"Reply-To: {$from}".PHP_EOL.
			"MIME-Version: 1.0".PHP_EOL.
			"Content-Type: multipart/mixed; boundary=\"{$boundary}\"".PHP_EOL;
			
		$content =
			"--{$boundary}".PHP_EOL.
			"Content-type:text/plain; charset=iso-8559-1".PHP_EOL.
			"Content-Transfer-Encoding: 7bit".PHP_EOL.
			PHP_EOL.
			$content.PHP_EOL.
			PHP_EOL;
			
		foreach ($settings['attachments'] as $k => $v) {
			$attachment = chunk_split(base64_encode($v));
			$content .=
				"--{$boundary}".PHP_EOL.
				"Content-type: application/octet-stream; name=\"{$k}\"".PHP_EOL.
				"Content-Transfer-Encoding: base64".PHP_EOL.
				"Content-Disposition: attachment; filename=\"{$k}\"".PHP_EOL.
				PHP_EOL.
				$attachment.PHP_EOL.
				PHP_EOL;				
		}
		
		$content .=
			"--{$boundary}--";
			
		if (!mail($to, $subject, $content, $headers)) {
			throw new Exception('Error when sending email');
		}
	}
	
	// check content for email header injection. The regex is copied from the intarwebz (http://snipplr.com/view/28723/check-for-email-header-injection/)
	// cause I'm lazy.
	function __check_header_injection($content) {
		return !preg_match('/\b^to+(?=:)\b|^content-type:|^cc:|^bcc:|^from:|^subject:|^mime-version:|^content-transfer-encoding:/im', $content);
	}
	// check referrer (to deny cross-site posts)
	function __check_referer() {
		return !empty($_SERVER['HTTP_REFERER']) || !strContains($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']);
	}
}

?>