<?php

class THtmlInputCombo extends THtmlFormCombo {

	public function createControls() {
		$this->control = zing::create('THtmlInput', array('type' => 'text'));
		parent::createControls();
	}
}


?>
