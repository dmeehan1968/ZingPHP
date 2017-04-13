<?php

class THtmlTableData extends THtmlControl {

	public function __construct($params = array()) {
		parent::__construct($params);
		
		$this->setTag('td');
	}
	
	public function setColspan($span) {
		$this->attributes['colspan'] = $span;
	}
	
	public function getColspan() {
		return $this->attributes['colspan'];
	}
	
	public function hasColspan() {
		return isset($this->attributes['colspan']);
	}
	
}

?>