<?php

class THtmlDefinition extends TCompositeControl {

	private	$dt;
	private	$dd;
	
	public function __construct($params = array()) {
	
		$this->dt = zing::create('THtmlDiv', array('tag' => 'dt'));
		$this->dd = zing::create('THtmlDiv', array('tag' => 'dd'));
		
		parent::__construct($params);
		
	}

	public function init() {
		
		$this->dt->doStatesUntil('preInit');
		$this->dd->doStatesUntil('preInit');
		
		foreach ($this->children as $child) {
			$this->dd->children[] = $child;
		}
		
		$this->children->deleteAll();

		$this->children[] = $this->dt;
		$this->children[] = $this->dd;
		
		parent::init();
	}
	
	public function getDefinitionControl() {
		return $this->dd;
	}
	
	public function setDefinitionControl($ctl) {
		$this->dd = $ctl;
	}

	public function getTermControl() {
		return $this->dt;
	}

	public function setTermControl($ctl) {
		$this->dt = $ctl;
	}
		
	public function setTerm($term) {
		$this->dt->setInnerText($term);
	}
	
	public function getTerm() {
		return $this->dt->getInnerText();
	}
	
	public function hasTerm() {
		return $this->dt->hasInnerText();
	}
	
	public function setDefinition($def) {
		$this->dd->setInnerText($def);
	}
	
	public function getDefinition() {
		return $this->dd->getInnerText();
	}
	
	public function hasDefinition() {
		return $this->dd->hasInnerText();
	}
	
	public function setClass($class) {
		$this->dt->setClass($class);
		$this->dd->setClass($class);
	}
	
	public function getClass() {
		return $this->dt->getClass();
	}
	
	public function hasClass() {
		return $this->dt->hasClass();
	}
	
	public function setCallbacks($callbacks) {
		$this->dd->setCallbacks($callbacks);
	}
	
	public function getCallbacks() {
		return $this->dd->getCallbacks();
	}
	
	public function hasCallbacks() {
		return $this->dd->hasCallbacks();
	}
	
	public function render() {
		$visible = $this->dd->getVisible();
		if ($visible & THtmlControl::VIS_CHILDREN) {
			ob_start();
			$this->dd->setVisible(THtmlControl::VIS_CHILDREN);
			$this->dd->render();
			$output = trim(ob_get_contents());
			ob_end_clean();
			if (!empty($output)) {
				$this->dd->setVisible(false);
				parent::render();
				$this->dd->renderPreChildren();
				echo $output;
				$this->dd->renderPostChildren();
			}
			$this->dd->setVisible($visible);
		}
	}
	
	public function bind() {
	
		if ($this->hasBoundObject() && $this->hasBoundProperty()) {
			$object = $this->getBoundObject();
			$property = $this->getBoundProperty();
			$this->dd->setInnerText(TControl::resolveBoundValue($object, $property));
		}
		
		parent::bind();
	}
}

?>