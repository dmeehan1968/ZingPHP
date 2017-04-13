<?php

class THtmlStyle extends THtmlHeadComponent {

	private $condition;
	
	public function preInit() {
		$this->setTag('style');
		parent::preInit();
	}
	
	public function setType($type) {
		$this->attributes['type'] = $type;
	}
	
	public function hasType() {
		return isset($this->attributes['type']);
	}
	
	public function getType() {
		return $this->attributes['type'];
	}
	
	public function setRel($rel) {
		$this->attributes['rel'] = $rel;
	}
	
	public function hasRel() {
		return isset($this->attributes['rel']);
	}
	
	public function getRel() {
		return $this->attributes['rel'];
	}
	
	public function setHref($href) {
		$this->attributes['href'] = $href;
	}
	
	public function hasHref() {
		return isset($this->attributes['href']);
	}
	
	public function getHref() {
		return $this->attributes['href'];
	}

	public function setMedia($media) {
		$this->attributes['media'] = $media;
	}
	
	public function hasMedia() {
		return isset($this->attributes['media']);
	}
	
	public function getMedia() {
		return $this->attributes['media'];
	}
		
	public function setConditional($condition) {
		if (!empty($condition)) {
			$this->condition = $condition;
		} else {
			unset($this->condition);
		}
	}
	
	public function hasConditional() {
		return isset($this->condition);
	}
	
	public function getConditional() {
		return $this->condition;
	}
	
	public function updatePlaceholder($ph) {
		if ($this->getOverwrite()) {
			$ph->styles->deleteAll();
		}
		
		$ph->addStyle($this);
	}
	
	public function render() {
		if ($this->hasHref()) {
			$this->setTag('link');
		}
		if ($this->hasConditional()) {
			echo '<!--[if ' . $this->getConditional() . ']>';
		}
		parent::render();
		if ($this->hasConditional()) {
			echo '<![endif]-->';
		}
	}
}

?>