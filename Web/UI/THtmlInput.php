<?php

class THtmlInput extends THtmlControl {

	function init() {
	
		parent::init();

		$this->setTag('input');
	}

	public function setId($id) {
		parent::setId($id);
		if (!$this->hasName()) {
			$this->setName($id);
		}
	}
	
	public function setType($type) {
		$this->attributes['type'] = $type;
	}
	
	public function getType() {
		return $this->attributes['type'];
	}
	
	public function hasType() {
		return isset($this->attributes['type']);
	}

	public function setValue($value) {
		$this->attributes['value'] = $value;
	}
	
	public function getValue() {
		return $this->attributes['value'];
	}
	
	public function hasValue() {
		return strlen($this->attributes['value']) ? true : false;
	}
	
	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$property = $this->getBoundProperty();
			$object = $this->getBoundObject();
			$value = TControl::resolveBoundValue($object, $property);
			$this->setValue($value);
		}
	}
	
	public function setDisabled($disabled) {
		if (zing::evaluateAsBoolean($disabled)) {
			$this->attributes['disabled'] = '1';
		} else {
			unset($this->attributes['disabled']);
		}
	}
	
	public function  isDisabled() {
		return isset($this->attributes['disabled']);
	}

	public function preRender() {
		if (! $this->hasType()) {
			$this->setType('text');
		}

		$this->addClass($this->getType());
		
		parent::preRender();
	}
	
	public function post() {
	
		parent::post();

		$sess = TSession::getInstance();
		if ($this->hasId() && isset($sess->app->post[$this->getId()]) && !is_array($sess->app->post[$this->getId()])) {
			$this->setValue($sess->app->post[$this->getId()]);
		}
	}
}


?>