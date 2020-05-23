<?php

class THtmlSelectOption extends THtmlControl {

	public function preRender() {

		parent::preRender();

		if ($this->getTag() == 'div') {
			$this->setTag('option');
		}
	}

	public function setValue($value) {
		$this->attributes['value'] = $value;
	}

	public function getValue() {
		return $this->attributes['value'];
	}

	public function hasValue() {
		return isset($this->attributes['value']);
	}

	public function setSelected($select = 'selected') {
		if (is_null($select)) {
			unset($this->attributes['selected']);
		} else {
			$this->attributes['selected'] = $select;
		}
	}

	public function getSelected() {
		return $this->attributes['selected'];
	}

	public function hasSelected() {
		return isset($this->attributes['selected']);
	}
}

?>
