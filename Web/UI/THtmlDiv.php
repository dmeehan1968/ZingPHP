<?php

class THtmlDiv extends THtmlControl {

	public function render() {
		if (! $this->getHideWhenEmpty() || $this->hasInnerContent()) {
			parent::render();
		}
	}

	private	$hideWhenEmpty = true;
	
	public function setHideWhenEmpty($hide) {
		$this->hideWhenEmpty = zing::evaluateAsBoolean($hide);
	}
	
	public function getHideWhenEmpty() {
		return $this->hideWhenEmpty;
	}
	
}

?>