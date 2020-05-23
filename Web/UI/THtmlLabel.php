<?php

class THtmlLabel extends THtmlDiv {

	public function init() {
		parent::init();

		$this->setTag('label');
	}

	public function setFor($for) {
		$this->attributes['for'] = $for;
	}

	public function getFor() {
		return $this->attributes['for'];
	}

	public function hasFor() {
		return isset($this->attributes['for']);
	}

}

?>
