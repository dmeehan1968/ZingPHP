<?php

class THtmlCheckboxGroupCombo extends THtmlFormCombo {

	public function createControls() {

		$this->control = zing::create('THtmlCheckboxGroup');
		parent::createControls();
	}

	public function init() {
		$this->addClass('form-checkbox-group-combo');
		parent::init();
	}

	public function setBoundProperty($property) {
		$this->control->setBoundProperty($property);
	}

	public function setOnItemRender($event) {
		$this->control->setOnItemRender($event);
	}
}

?>
