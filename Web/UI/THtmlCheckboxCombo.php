<?php

class THtmlCheckboxCombo extends THtmlDiv {

	public $label;
	public $control;

	public function __construct($params = array()) {
		$this->label = zing::create('THtmlLabel');
		$this->createControl();
		parent::__construct($params);

		$this->children[] = $this->control;
		$this->children[] = $this->label;

	}

	public function createControl() {
		$this->control = zing::create('THtmlCheckbox');
	}

	public function init() {
		$this->addClass('form-checkbox-combo');
		parent::init();
	}

	private $controlId;

	public function setId($id) {
		$this->controlId = $id;
		$this->addClass($id);
	}

	public function hasId() {
		return isset($this->controlId);
	}

	public function getId() {
		return $this->controlId;
	}

	public function bind() {
		if ($this->hasBoundObject() && $this->hasBoundProperty()) {
			$property = $this->getBoundProperty();
			list($value, $label) = explode('|', $property);
			$object = $this->getBoundObject();
			$this->control->setValue($object->$value);
			$this->setControlId($this->getId(), $object->$value);
			$this->label->setInnerText($object->$label);
			$this->control->setChecked($object->selected);
		} else {
			parent::bind();
		}
	}

	public function render() {
		$this->setControlId($this->getId(), $this->getvalue());
		parent::render();
	}

	public function setControlId($id, $value) {
		$ctlId = $id . '[' . $value . ']';
		$this->control->setId($ctlId);
		$this->label->setFor($ctlId);
	}

	public function setLabel($label) {
		$this->label->setInnerText($label);
	}

	public function getLabel() {
		return $this->label->getInnerText();
	}

	public function setValue($value) {
		$this->control->setValue($value);
	}

	public function getValue() {
		return $this->control->getValue();
	}

	public function setDisabled($disabled) {
		$this->control->setDisabled($disabled);
	}

	public function isDisabled() {
		return $this->control->isDisabled();
	}
}


?>
