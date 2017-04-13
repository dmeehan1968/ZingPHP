<?php

class THtmlFileUploadCombo extends THtmlFormCombo {

	public function createControls() {
		$this->control = zing::create('THtmlFileUpload');
		parent::createControls();
	}
}


?>