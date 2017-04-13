<?php

class THtmlSelectCombo extends THtmlFormCombo {
	
	public function createControls() {
		$this->control = zing::create('THtmlSelect');
		parent::createControls();
	}
		
	public function setSelected($select) {
		$this->control->setSelected($select);
	}
	
	public function getSelected() {
		return $this->control->getSelected();
	}
	
	public function hasSelected() {
		return $this->control->hasSelected();
	}
	
	public function setMultiple($multi) {
		$this->control->setMultiple($multi);
	}
	
	public function hasMultiple() {
		return $this->control->hasMultiple();
	}
	
	public function getMultiple() {
		return $this->control->getMultiple();
	}
	
	public function setDisabled($disabled) {
		$this->control->setDisabled($disabled);
	}
	
	public function getDisabled() {
		return $this->control->getDisabled();
	}
	
	public function hasDisabled() {
		return $this->control->hasDisabled();
	}
	
	public function setSize($size) {
		$this->control->setSize($size);
	}
	
	public function getSize() {
		return $this->control->getSize();
	}
	
	public function hasSize() {
		return $this->control->hasSize();
	}
	
	public function setBoundProperty($property) {
		$this->control->setBoundProperty($property);
	}
	
	public function getBoundProperty() {
		return $this->control->getBoundProperty();
	}
	
	public function hasBoundProperty() {
		return $this->control->hasBoundProperty();
	}
	
	public function setOptions($options) {
		$options = explode('|', $options);
		$objects = array();
		foreach ($options as $option) {
			$object = $objects[] = new StdClass;
			$object->id = $option;
			$object->value = $option;
		}
		$this->setBoundObject($objects);
		$this->setBoundProperty('id|value');
	}
}

?>