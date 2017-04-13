<?php

class THtmlCheckboxGroup extends THtmlDiv {

	public $repeater;
	public $control;
	
	public function __construct($params = array()) {

		$this->repeater = zing::create('TRepeater');
		$this->createControl();
		
		parent::__construct($params);
		
		$this->children[] = $this->repeater;
		$this->children[] = zing::create('THtmlBr', array('class' => 'clear'));
		$this->repeater->children[] = $this->control;
	}
	
	public function createControl() {
		$this->control = zing::create('THtmlCheckboxCombo');
	}

	public function init() {
		$this->addClass('form-checkbox-group');
		parent::init();
	}

	public function setId($id) {
		$this->control->setId($id);
	}
	
	public function getId() {
		return $this->control->getId();
	}
	
	public function hasId() {
		return $this->control->hasId();
	}
	
	public function setBoundProperty($property) {
		$this->control->setBoundProperty($property);
	}
	
	public function setOnItemRender($event) {
		$this->control->setOnRender($event);
	}

	public function setDisabled($disable) {
		$this->control->setDisabled($disable);
	}
	
	public function getDisabled() {
		return $this->control->getDisabled();
	}

}

?>