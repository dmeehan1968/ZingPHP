<?php

class THtmlRadioCombo extends THtmlCheckboxCombo {

	public function createControl() {
		$this->control = zing::create('THtmlRadioButton');
	}

	public function init() {
		$this->addClass('form-radio-combo');
		THtmlDiv::init();
	}

	public function setControlId($id, $value) {
		$ctlId = $id . '[' . $value . ']';
		$this->control->setId($ctlId);
		$this->label->setFor($ctlId);
		$this->control->setName($id);
	}

}


?>
