<?php

class THtmlScriptInline extends THtmlControl {

	public function __construct($params = array()) {
		parent::__construct();
		$this->setTag('script');
		$this->setType('text/javascript');
		$this->parseParams($params);
	}
	
	private $src;
	
	public function setSrc($src) {
		$this->attributes['src'] = $src;
	}
	
	public function getSrc() {
		return $this->attributes['src'];
	}
	
	public function hasSrc() {
		return ! empty($this->attributes['src']);
	}
	
	private $type;
	
	public function setType($type) {
		$this->attributes['type'] = $type;
	}
	
	public function hasType() {
		return ! empty($this->attributes['type']);
	}
	
	public function getType() {
		return $this->attributes['type'];
	}

	public function setInnerText($text) {
		$this->children->deleteAll();
		$this->children[] = zing::create('TRawOutput', array('innerText' => $text));
	}
	
	public function getInnerText() {
		foreach ($this->children as $child) {
			if ($child instanceof TRawOutput) {
				return $child->getValue();
			}
		}
		return '';
	}
	
}

?>