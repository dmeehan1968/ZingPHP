<?php

class TextParser_Renderer {

	public $rule;
	protected $parser;
	protected $delim;

	public function __construct(TextParser $parser) {
		$this->parser = $parser;
		$this->delim = $parser->getDelim();
		$parts = explode('_', get_class($this));
		$this->rule = array_pop($parts);
	}

	public function render($params) {
	}
}

?>
