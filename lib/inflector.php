<?php
/* Based on CakePHP's Inflector */

class Inflector extends Object {
	// From CakePHP: Returns the given lower_case_and_underscored_word as a CamelCased word.
	// This is for class names
	function camelize($w) { return $this->camelcase($w); }
	function camelcase($lowerCaseAndUnderscoredWord) {
		return str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
	}
	
	// From CakePHP (as variable()). Returns the given lower_case_and_underscored_word as a camelBacked word
	// This is for variable names
	function camelback($lowerCaseAndUnderscoredWord) {
		$c = $this->camelcase($lowerCaseAndUnderscoredWord);
		$replace = strtolower(substr($c, 0, 1));
		return preg_replace('/\\w/', $replace, $c, 1);
	}
	
	// From CakePHP: Returns the given camelCasedWord as an underscored_word.
	// this is for filenames and database fields and tables
	function underscore($camelCasedWord) {
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
	}

	// Returns the given underscored_word_group as a Human Readable Word Group.
	function humanize($lowerCaseAndUnderscoredWord) {
		return ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord));
	}
	
	// Returns 'hello_world' as 'Hello world'
	function sentence($lowerCaseAndUnderscoredWord) {
		return ucfirst(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
	}
};

?>