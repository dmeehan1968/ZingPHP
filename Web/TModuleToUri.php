<?php

class TModuleToUri extends TControl {

	private	$module;
	private	$params = array();
	
	public function setModule($module) {
		$this->module = $module;
	}
	
	public function getModule() {
		return $this->module;
	}
	
	public function hasModule() {
		return isset($this->module);
	}
	
	public function __call($name, $params) {
		$name = strtolower($name);
		
		if (substr($name,0,3) == 'set') {
			$name = substr($name,3);
			$this->params[$name] = urlencode(strtolower(array_shift($params)));
		}	
	}
	
	public function render() {
		parent::render();
		if ($this->getVisible()) {
			$sess = TSession::getInstance();
			echo $sess->app->getModuleUri($this->getModule(), $this->params);
		}
	}

}

?>