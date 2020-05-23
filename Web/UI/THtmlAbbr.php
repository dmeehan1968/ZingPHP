<?php

class THtmlAbbr extends THtmlControl {

	public function init() {
		$this->setTag('abbr');
	}
	
	public function setTitle($title) {
		$this->attributes['title'] = $title;
	}
	
	public function getTitle() {
		return $this->attributes['title'];
	}
	
	public function hasTitle() {
		return isset($this->attributes['title']);
	}
	
}


?>
