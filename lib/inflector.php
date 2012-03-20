<?php

class Inflector {
	// takes "underscored_lowercase_words", returns "CamelCaseWords"
	static function camelcase($w) { return Inflector::camelize($w); }
	static function camelise($w) { return Inflector::camelize($w); }
	static function camelize($w) {
		$underscoredToWords = str_replace('_', ' ', $w);
		$uppercased = ucwords($underscoredToWords);
		return str_replace(' ', '',$uppercased);
	}

	// takes "underscored_lowercase_words", returns "pascalCasedWords"
	static function pascal($w) {
		$c = Inflector::camelize($w);
		$refront = strtolower(substr($c, 0, 1));
		$p = preg_replace('/\\w/', $refront, $c, 1);
		return $p;
	}

	// takes "pascalCaseWords" or "CamelCaseWords" and returns "underscored_lowercase_words"
	function underscore($w) {
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $w));
	}

	// takes "underscored_lowercase_words" and returns "Human Readable Words"
	function humanise($w) { return Inflector::humanize($w); }
	function humanize($w) {
		return ucwords(str_replace("_", " ", $w));
	}

	// takes "underscored_lowercase_words" and returns "A human readable sentence"
	function sentence($w) {
		return ucfirst(str_replace('_', ' ', $w));
	}
}

?>