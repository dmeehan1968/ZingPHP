<?php

class THtmlRadioGroup extends THtmlCheckboxGroup {

	public function createControl() {
		$this->control = zing::create('THtmlRadioCombo');
	}

	public function init() {
		$this->addClass('form-radio-group');
		THtmlDiv::init();
	}
}

?>
