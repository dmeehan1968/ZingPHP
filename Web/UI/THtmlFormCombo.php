<?php

abstract class THtmlFormCombo extends THtmlDiv {

	public $label;
	public $control;	// assignment delegated to inheriting class
	public $error;
	public $help;
	public $required;
	public $inlineHelp = false;

	public function __construct($params = array()) {

		$this->createControls();
		
		parent::__construct($params);

		$this->assignChildren();
	}
	
	public function createControls() {
		$this->label = zing::create('THtmlLabel', array('innerText' => 'label:'));
		$this->error = zing::create('THtmlDiv', array('class' => 'form-error'));
		$this->help = zing::create('THtmlDiv', array('class' => 'form-help'));
		$this->required = zing::create('THtmlAbbr' , array('class' => 'form-required', 'title' => 'required', 'innerText' => '*'));

		$this->error->setVisible(false);
		$this->help->setVisible(false);
		$this->required->setVisible(false);
	}
	
	public function assignChildren() {
		$this->children[] = $this->error;
		$this->children[] = $this->label;
		if ($this->inlineHelp) {
			$this->help->setTag('span');
			$this->label->children[] = zing::create('TPlainText', array('value' => ' '));
			$this->label->children[] = $this->help;
		}
		$this->label->children[] = $this->required;
		$this->children[] = zing::create('THtmlBr');
		$this->children[] = $this->control;
		if (! $this->inlineHelp) {
			$this->children[] = $this->help;
		}
		$this->children[] = zing::create('THtmlBr', array('class' => 'clear'));
	}
		
	public function setId($id) {
		$this->control->setId($id);
		$this->label->setFor($id);
	}

	public function getId() {
		return $this->control->getId();
	}
	
	public function hasId() {
		return $this->control->hasId();
	}
			
	public function setName($name) {
		$this->control->setName($name);
	}
	
	public function getName() {
		return $this->control->getName();
	}
	
	public function hasName() {
		return $this->control->hasName();
	}

	public function setLabel($label) {
		$this->label->setInnerText($label);
	}
	
	public function getLabel() {
		return $this->label->getInnerText();
	}
	
	public function hasLabel() {
		return $this->label->hasInnerText();
	}
	
	public function setLabelOnClick($onclick) {
		$this->label->setOnClick($onclick);
	}
	
	/**
	 * Assume that calls to undefined methods are calls to the embedded control
	 */
	public function __call($method, $params) {
		if (method_exists($this->control, $method)) {
			return call_user_func_array(array($this->control, $method), $params);
		} else {
			throw new Exception('undefined method: ' . $method . ' for class ' . get_class($this));
		}
	}
	
	public function setValue($value) {
		if (method_exists($this->control, 'setValue')) {
			$this->control->setValue($value);
		} else {
			$this->control->setInnerText($value);
		}
	}
	
	public function getValue() {
		if (method_exists($this->control, 'getValue')) {
			return $this->control->getValue();
		} else {
			return $this->control->getInnerText();
		}
	}
	
	public function hasValue() {
		if (method_exists($this->control, 'hasValue')) {
			return $this->control->hasValue();
		} else {
			return $this->control->hasInnerText();
		}
	}

	public function setError($error) {
		$this->error->setInnerText($error);
		$this->error->setVisible($this->error->hasInnerText());
	}
	
	public function getError() {
		return $this->error->getInnerText();
	}
	
	public function hasError() {
		return $this->error->hasInnerText();
	}
	
	public function setRequired($required) {
		$this->required->setVisible(zing::evaluateasBoolean($required));
	}
	
	public function getRequired() {
		return $this->required->getVisible();
	}
	
	public function hasRequired() {
		return $this->required->hasInnerText();
	}
	
	public function setHelp($help) {
		$this->help->setInnerText($help);
		$this->help->setVisible($this->help->hasInnerText());
	}
	
	public function getHelp() {
		return $this->help->getInnerText();
	}
	
	public function hasHelp() {
		return $this->help->hasInnerText();
	}
	
	public function setInlineHelp($help) {
		$this->inlineHelp = zing::evaluateAsBoolean($help);
	}
	
	public function getInlineHelp() {
		return $this->inlineHelp;
	}
	
	public function hasInlineHelp() {
		return isset($this->inlineHelp);
	}
	
	public function preRender() {
		parent::preRender();

		$this->addClass('form-combo');
		if ($this->getRequired()) {
			$this->addClass('form-required');
		}
		if ($this->hasError()) {
			$this->addClass('form-error');
		}
		$this->addClass($this->control->getId());
		if ($this->control instanceof THtmlControl) {
			$this->addClass($this->control->getTag());
		}

	}

	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$object = $this->getBoundObject();
			$property = $this->getBoundProperty();
			$value = TControl::resolveBoundValue($object, $property);
			$this->setValue($value);
		}
	}	
}

?>