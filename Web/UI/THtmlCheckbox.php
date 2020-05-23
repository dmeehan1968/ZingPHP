<?php

class THtmlCheckbox extends THtmlInput {

	public function init() {
		parent::init();

		$this->setType('checkbox');
	}

	public function setId($id) {
		parent::setId($id);
		$this->setName($id);
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

	public function setChecked($checked = true) {
		if ($checked) {
			$this->attributes['checked'] = '';
		} else {
			unset($this->attributes['checked']);
		}
	}

	public function getChecked() {
		return $this->attributes['checked'];
	}

	public function hasChecked() {
		return isset($this->attributes['checked']);
	}

}

?>
