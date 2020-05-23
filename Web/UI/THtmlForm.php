<?php

class THtmlForm extends THtmlControl {

	public function init() {
		$this->setTag('form');
		parent::init();
	}

	public function preRender() {
		if (!$this->hasMethod()) {
			$this->setMethod('post');
		}
		if (!$this->hasAction()) {
			$sess = TSession::getInstance();
			$this->setAction($sess->app->request->_uri);
		}

		parent::preRender();
	}

	public function setEncType($type) {
		$this->attributes['enctype'] = $type;
	}

	public function getEncType() {
		return $this->attributes['enctype'];
	}

	public function hasEncType() {
		return isset($this->attributes['enctype']);
	}

	public function setAction($action) {
		$this->attributes['action'] = $action;
	}

	public function getAction() {
		return $this->attributes['action'];
	}

	public function hasAction() {
		return isset($this->attributes['action']);
	}

	public function setMethod($method) {
		$this->attributes['method'] = $method;
	}

	public function getMethod() {
		return $this->attributes['method'];
	}

	public function hasMethod() {
		return isset($this->attributes['method']);
	}
}

?>
