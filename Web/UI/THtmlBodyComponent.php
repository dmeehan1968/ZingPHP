<?php

class THtmlBodyComponent extends THtmlControl {

	public function render() {
		// do nothing
	}

	public function setOnLoad($value) {
		$this->attributes['onload'] = $value;
	}

	public function setOnUnLoad($value) {
		$this->attributes['onunload'] = $value;
	}
}

?>
