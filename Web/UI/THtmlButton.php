<?php

class THtmlButton extends THtmlInput {

	private $jsOnClick;
	
	public function setJSOnClick($script) {
		$this->rawAttributes['onClick'] = $script;
	}
	
	public function getJSOnClick() {
		return $this->rawAttributes['onClick'];
	}
	
	public function hasJSOnClick() {
		return !empty($this->rawAttributes['onClick']);
	}

	private $onClick;
	
	public function setOnClick($action) {
		$this->onClick = $action;
	}
	
	public function getOnClick() {
		return $this->onClick;
	}
	
	public function hasOnClick() {
		return isset($this->onClick);
	}
	
	public function post() {
		parent::post();
		$sess = TSession::getInstance();
		$fire = false;
		if ($this->hasId() && $this->hasOnClick()) {
			$id = $this->getId();
			list($id) = explode('[', $id);
			if ($sess->app->post[$id]) {
				if (is_array($sess->app->post[$id])) {
					foreach ($sess->app->post[$id] as $index => $value) {
						if (!empty($value)) {
							$fire = true;
							$params = array('index' => $index, 'value' => $value);
							break;
						}
					}
				} else {
					$fire = true;
					$params = $sess->app->post;
				}
					
				if ($fire) {
					$this->fireEvent($this->getOnClick(), $this, $params);
				}
			}
		}
	}

	public function preRender() {
		if (! $this->hasType()) {
			$this->setType('submit');
		}
		if (! $this->hasValue()) {
			$this->setValue($this->getId());
		}
		if (!$this->hasClass()) {
			$this->setClass('button');
		}
		
		parent::preRender();
	}		
}



?>