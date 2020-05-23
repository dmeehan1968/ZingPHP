<?php

class THtmlInlineScript extends THtmlDiv {

	public function preInit() {
		$this->setTag('script');
		if (!$this->hasType()) {
			$this->setType('text/javascript');
		}
		parent::preInit();
	}

	public function hasInnerContent() {
		return true;  // always render open and closing tag, as script don't support abbreviated form
	}

	private $type;

	public function setType($type) {
		$this->attributes['type'] = $type;
	}

	public function hasType() {
		return isset($this->attributes['type']);
	}

	public function getType() {
		return $this->attributes['type'];
	}

	private $src;

	public function setSrc($src) {
		$this->attributes['src'] = $src;
	}

	public function hasSrc() {
		return isset($this->attributes['src']);
	}

	public function getSrc() {
		return $this->attributes['src'];
	}

}

?>
