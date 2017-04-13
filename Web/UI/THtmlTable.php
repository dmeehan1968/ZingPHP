<?php

class THtmlTable extends THtmlControl {

	private	$caption;
	private	$head;
	private	$body;
	private	$repeater;
		
	public function __construct($params = array()) {
		parent::__construct($params);
		
		$this->setTag('table');
		if (! isset($this->attributes['cellspacing'])) {
			$this->attributes['cellspacing'] = 0;
		}
	}

	public function setCaption($caption) {
		$this->caption = $caption;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function hasCaption() {
		return isset($this->caption);
	}
	
	public function initComplete() {
	
		$this->head = zing::create('THtmlControl', array('tag' => 'thead'));
		$row = $this->head->children[] = zing::create('THtmlTableRow');

		foreach ($this->children as $column) {
			if ($column instanceof THtmlTableColumn) {
				$row->children[] = $column->createHeadTableData();
			} else {
				$row->children[] = $column;
			}
		}

		$this->body = zing::create('THtmlControl', array('tag' => 'tbody'));
		$this->repeater = $rpt = $this->body->children[] = zing::create('TRepeater');
		if ($this->hasOnRepeat()) {
			$rpt->setOnRepeat($this->getOnRepeat());
		}
		if ($this->hasBoundProperty()) {
			$rpt->setBoundProperty($this->getBoundProperty());
		}
		$row = $rpt->children[] = zing::create('THtmlTableRow');
		foreach ($this->children as $column) {
			if ($column instanceof THtmlTableColumn) {
				$row->children[] = $column->createBodyTableData();
			} else {
				$row->children[] = $column;
			}
		}
		$row->setOnRender('setRowClass');
					
		$this->head->preInit();
		$this->body->preInit();
		$this->head->init();
		$this->body->init();
		
		$this->children->deleteAll();
		$this->children[] = $this->head;
		$this->children[] = $this->body;
	
		parent::initComplete();
	}

	private $onRepeat;
	
	public function setOnRepeat($repeat) {
		$this->onRepeat = $repeat;
	}
	
	public function getOnRepeat() {
		return $this->onRepeat;
	}
	
	public function hasOnRepeat() {
		return isset($this->onRepeat);
	}

	public function setBoundObject($object) {
		$this->repeater->setBoundObject($object);
	}

	public function setRowClass($control, $params) {
		$control->setClass($control->getContainer()->getIterations() % 2 ? 'odd' : 'even');
	}	

	public function bind() {
		// skip THtmlControl's bind(), as it would cause innertext to be added, not relevant to table controls.
		TCompositeControl::bind();
	}	
}

?>