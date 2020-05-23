<?php

class TextParser_Parser {

	public $parser;
	public $rule;
	public $regexp = '/.*/';

	public function __construct(TextParser $parser) {
		$this->parser = $parser;
		$parts = explode('_', get_class($this));
		$this->rule = array_pop($parts);
	}

	public function addToken($type, $params = array()) {
		return $this->parser->addToken($this->rule, $type, $params);
	}

	public function parse($text, &$replaced) {
		$replaced = 0;
		$text = preg_replace_callback($this->regexp, array(&$this, 'onMatch'), $text, -1, $replaced);
		return $text;
	}

	public function onMatch($match) {
		return $match[0];
	}
}

?>
