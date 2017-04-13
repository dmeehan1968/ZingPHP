<?php

class TPlainText extends TControl {

	private $value;
	
	public function getValue() {
		return $this->value;
	}
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function hasValue() {
		return isset($this->value);
	}
	
	public function render() {
		parent::render();
		echo $this->getValue();
	}
	
}

?>