<?php

class TYuiMenuItem extends TCompositeControl {
	
	public function getYuiMenuItemClass() {
		return 'yuimenuitem';
	}
	
	public function getYuiMenuItemLabelClass() {
		return 'yuimenuitemlabel';
	}
	
	private $li;
	
	public function __construct($params = array()) {
		$this->li = zing::create('THtmlDiv', array('tag' => 'li', 'class' => $this->getYuiMenuItemClass()));
		parent::__construct($params);
	}
	
	public function addClass($class) {
		$this->li->addClass($class);
	}
	
	public function preRender() {
		
		$children = clone $this->children;
		$this->children->deleteAll();
		
		$this->children[] = $this->li;
		
		$container = $this->li;
		foreach ($children as $child) {
			if ($child instanceof TPlainText) {
				if (trim($child->getValue()) != '') {
					$container = $this->li->children[] = zing::create('THtmlDiv', array('tag' => 'span', 'class' => $this->getYuiMenuItemLabelClass()));
				} else {
					continue;
				}
			} else {
				$child->addClass($this->getYuiMenuItemLabelClass());
			}
			break;
		}
		$this->li->doStatesUntil('postComplete');

		$container->children = $children;
		
		parent::preRender();
	}

}

?>