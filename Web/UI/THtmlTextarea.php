<?php

class THtmlTextarea extends THtmlControl {

	public function init() {
		parent::init();
	
		$this->setTag('textarea');
	}

	public function setRows($rows) {
		$this->attributes['rows'] = $rows;
	}
	
	public function getRows() {	
		return $this->attributes['rows'];
	}
	
	public function hasRows() {
		return isset($this->attributes['rows']);
	}
	
	public function setCols($cols) {
		$this->attributes['cols'] = $cols;
	}
	
	public function getCols() {	
		return $this->attributes['cols'];
	}
	
	public function hasCols() {
		return isset($this->attributes['cols']);
	}
	
	public function preRender() {
		parent::preRender();
		
		if (! $this->hasName()) {
			$this->setName($this->getId());
		}
	}
	
	public function hasInnerContent() {
		return true;
	}	

	public function post() {
	
		parent::post();

		$sess = TSession::getInstance();
		if ($this->hasId() && isset($sess->app->post[$this->getId()])) {
			$this->setInnerText($sess->app->post[$this->getId()]);
		}
	}
}
	
?>
