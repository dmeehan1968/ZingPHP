<?php

class THtmlTableColumn extends THtmlControl {

	private	$title;
	
	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function hasTitle() {
		return isset($this->title);
	}
	
	public function createHeadTableData() {
		$params = array();
		if ($this->hasTitle()) {
			$params['innerText'] = $this->getTitle();
		}
		if ($this->hasClass()) {
			$params['class'] = $this->getClass();
		}
		if ($this->hasStyle()) {
			$params['style'] = $this->getStyle();
		}
		$td = zing::create('THtmlTableData', $params);
		
		$td->cloneAuth($this);
		return $td;
	}
	
	public function createBodyTableData() {
		$params = array();
		if ($this->hasClass()) {
			$params['class'] = $this->getClass();
		}
		if ($this->hasStyle()) {
			$params['style'] = $this->getStyle();
		}
		if ($this->hasBoundProperty()) {
			$params['boundProperty'] = $this->getBoundProperty();
		}
		if ($this->hasOnRender()) {
			$params['onRender'] = $this->getOnRender();
		}
		$td = zing::create('THtmlTableData', $params);
		if ($this->hasChildren()) {
			foreach ($this->children as $id => $child) {
				$td->children[$id] = $child;
			}
		}
		
		$td->cloneAuth($this);
		return $td;
	}
	
	public function getHeadProperties() {
		return array('hasTitle', 'hasClass', 'hasStyle');
	}
	
	public function getBodyProperties() {
		return array('hasClass', 'hasStyle', 'hasBoundProperty', 'hasOnRender');
	}
}

?>