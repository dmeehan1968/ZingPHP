<?php

class THtmlTextareaCombo extends THtmlFormCombo {

	public function createControls() {
		$this->control = zing::create('THtmlTextarea');
		parent::createControls();
	}

}


?>
