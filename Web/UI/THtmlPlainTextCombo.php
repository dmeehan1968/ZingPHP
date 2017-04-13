<?php

class THtmlPlainTextCombo extends THtmlFormCombo {
	
	public function createControls() {
		$this->control = zing::create('THtmlDiv');
		
		parent::createControls();
	}
	
	public function load() {
		$this->addClass('plaintext-combo');
		parent::load();
	}
	
	public function setInnerText($text) {
		$this->control->setInnerText($text);
	}
	
	public function getInnerText() {
		return $this->control->getInnerText();
	}
}

?>