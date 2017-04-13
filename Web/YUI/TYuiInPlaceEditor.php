<?php

class TYuiInPlaceEditor extends THtmlDiv {

	private $loader;
	private $raw;
	
	public function __construct($params = array()) {

		$this->loader = zing::create('TYuiLoader');
		$this->loader->setAddModule('name: "TYuiInPlaceEditorCSS", type: "css", fullpath: "/Zing/Web/YUI/TYuiInPlaceEditor.css"');
		$this->loader->setAddModule('name: "TYuiInPlaceEditor", type: "js", fullpath: "/Zing/Web/YUI/TYuiInPlaceEditor.js", requires: [ "editor", "menu", "TYuiInPlaceEditorCSS" ]');
		$this->loader->setRequire('TYuiInPlaceEditor, container, dragdrop');

		$this->innerDiv = zing::create('THtmlDiv', array('class' => 'editor-inactive'));
		$this->raw = $this->innerDiv->children[] = zing::create('TRawOutput');
		
		parent::__construct($params);
		
		$this->children[] = $this->loader;
		$this->children[] = $this->innerDiv;
	}
	
	public function init() {
		$this->addClass('in-place-editor');
		parent::init();
	}
	
	public function setInnerText($text) {
		$this->raw->setInnerText($text);
	}
	
	public function getInnerText() {
		return $this->raw->getInnerText();
	}
	
	public function setAuthPermsEditor($perms) {
		$this->loader->setAuthPerms($perms);
	}

	public function preRender() {
		$this->loader->setOnSuccess('var ipe = new YAHOO.zing.TYuiInPlaceEditor("' . $this->getId() . '");');
		parent::preRender();
	}
}

?>