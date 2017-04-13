<?php

class THtmlAttributeText extends TControl {

	private	$value;
	
	public function setValue($value) {
		$this->value = $value;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function hasValue() {
		return isset($this->value);
	}
	
	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$property = $this->getBoundProperty();
			$object = $this->getBoundObject();
			$value = TControl::resolveBoundValue($object, $property);

			$this->setValue($value);
		}
		
		parent::bind();
	}	
	
	public function render() {
		parent::render();

		if ($this->getVisible()) {
			echo htmlentities(str_replace("\r\n", " ", strip_tags($this->value)));
		}
	}

}

?>
