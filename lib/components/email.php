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
		if (!isset($settings['to'])) throw new Exception('To address was not provided');
		$to = $settings['to'];
		$subject = isset($settings['subject']) ? $settings['subject'] : '';
		$content = isset($settings['content']) ? $settings['content'] : '';
		$from = isset($settings['from']) ? $settings['from'] : $to;
		$attachments = isset($settings['attachments']) ? $settings['attachments'] : array();
		$contentType = isset($settings['content_type']) ? $settings['content_type'] : 'text/plain';

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
			"Content-type:{$contentType}; charset=iso-8559-1".PHP_EOL.
			"Content-Transfer-Encoding: 7bit".PHP_EOL.
			PHP_EOL.
			$content.PHP_EOL.
			PHP_EOL;
			
		foreach ($attachments as $filename => $data) {
			$data = chunk_split(base64_encode($data));
			$content .=
				"--{$boundary}".PHP_EOL.
				"Content-type: application/octet-stream; name=\"{$filename}\"".PHP_EOL.
				"Content-Transfer-Encoding: base64".PHP_EOL.
				"Content-Disposition: attachment; filename=\"{$filename}\"".PHP_EOL.
				PHP_EOL.
				$data.PHP_EOL.
				PHP_EOL;				
		}
		
		$content .=
			"--{$boundary}--";
			
		if (!mail($to, $subject, $content, $headers)) {
			throw new Exception('Error when sending email');
		}
	}
	
	function __check_header_injection($content) {
		return !preg_match('/\b^to+(?=:)\b|^content-type:|^cc:|^bcc:|^from:|^subject:|^mime-version:|^content-transfer-encoding:/im', $content);
	}

	function __check_referer() {
		return !empty($_SERVER['HTTP_REFERER']) || !strContains($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']);
	}
}

?>