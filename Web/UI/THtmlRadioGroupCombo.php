<?php

class THtmlRadioGroupCombo extends THtmlCheckboxGroupCombo {

	public function createControls() {

		$this->control = zing::create('THtmlRadioGroup');
		THtmlFormCombo::createControls();
	}

	public function init() {
		$this->addClass('form-radio-group-combo');
		THtmlFormCombo::init();
	}

}

?>
