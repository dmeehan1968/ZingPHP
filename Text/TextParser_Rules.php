<?php

class TextParser_Rules {
	
	protected $rules = array();
	
	public function addRule($rule) {
		$this->rules[] = $rule;
	}
	
	public function loadParsers(TextParser $parser) {
		asort($this->rules);
		foreach ($this->rules as $rule => $order) {
			$class = $this->getClass() . '_Parser_' . $rule;
			$parser->addParser(new $class($parser));
		}
	}
	
	public function loadRenderer(TextParser $parser, $format, $rule) {
		$class = $format . '_Render_' . $rule;
		$parser->addRenderer($rule, new $class($parser));
	}
	
	public function getClass() {
		return '<unknown>';
	}
}

?>