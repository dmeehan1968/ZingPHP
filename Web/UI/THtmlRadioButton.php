<?php

class THtmlRadioButton extends THtmlCheckbox {

	public function init() {
		parent::init();
		$this->setType('radio');
	}
}

?>