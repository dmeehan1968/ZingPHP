<?php

class TYuiMenuBar extends TYuiMenu {

	public function __construct($params = array()) {
		$this->setShow(true);
		parent::__construct($params);
	}
	
	public function getYuiMenuClass() {
		return 'yuimenubar';
	}
	
	public function getYuiMenuObject() {
		return 'MenuBar';
	}
	
	public function getYuiMenuItemType() {
		return 'TYuiMenuBarItem';
	}

}

?>