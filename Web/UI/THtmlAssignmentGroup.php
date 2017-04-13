<?php

class THtmlAssignmentGroup extends THtmlFormCombo {
	
	private $assigned;
	private $available;
	private $addBtn;
	private $removeBtn;
	
	public function createControls() {
		$this->control = zing::create('TCompositeControl');
		$this->assigned = $this->control->children[] = zing::create('THtmlSelectCombo', array('multiple' => 1, 'class' => 'assigned'));
		$btns = $this->control->children[] = zing::create('THtmlDiv', array('tag' => 'span', 'class' => 'buttons'));
		$this->removeBtn = $btns->children[] = zing::create('THtmlButton', array('value' => '>>', 'onClick' => 'onRemove'));
		$this->addBtn = $btns->children[] = zing::create('THtmlButton', array('value' => '<<', 'onClick' => 'onAdd'));
		$this->available = $this->control->children[] = zing::create('THtmlSelectCombo', array('multiple' => 1, 'class' => 'available'));
		parent::createControls();
	}
	
	public function setSize($size) {
		$this->assigned->setSize($size);
		$this->available->setSize($size);
	}
	
	public function setAssignedLabel($label) {
		$this->assigned->setLabel($label);
	}
	
	public function setAvailableLabel($label) {
		$this->available->setLabel($label);
	}
	
	public function load() {
		$this->addClass('assignment-group');
		parent::load();
	}
	
	public function setId($id) {
		$this->assigned->setName('assigned' . $id);
		$this->available->setName('available' . $id);
		$this->addBtn->setId('add' . $id);
		$this->removeBtn->setId('remove' . $id);
		parent::setId($id);
	}
		
	public function setAvailableBoundObject($object) {
		$this->available->setBoundObject($object);
	}
	
	public function setAvailableBoundProperty($property) {
		$this->available->setBoundProperty($property);
	}
	
	public function setAssignedBoundObject($object) {
		$this->assigned->setBoundObject($object);
	}
	
	public function setAssignedBoundProperty($property) {
		$this->assigned->setBoundProperty($property);
	}
	
	public function onAdd($control, $params) {
		$this->fireEvent('onAdd' . $this->getId(), $this, $params['available' . $this->getId()]);
	}
	
	public function onRemove($control, $params) {
		$this->fireEvent('onRemove' . $this->getId(), $this, $params['assigned' . $this->getId()]);
	}
	

}

?>