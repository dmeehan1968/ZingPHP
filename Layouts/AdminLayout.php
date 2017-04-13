<?php

class AdminLayout extends TCachedLayout {

	public function preInit() {
		parent::preInit();
		$this->setCacheTimeout(-1);
	}
	
	public function render() {
		header('Content-Type: text/html; charset=ISO-8859-1');
		parent::render();
	}

	public function setUsername($control, $params) {
		$auth = TAuthentication::getInstance();
		$control->children->deleteAll();
		$control->setInnerText($auth->getUsername());
	}
	
	public function setFirstAndLast($control, $params) {
		$last = null;
		foreach ($control->children as $child) {
			if (is_null($last) && $child instanceof THtmlControl) {
				$child->addClass('first');
			}
			$last = $child;
		}
		if (!is_null($last) && $last instanceof THtmlControl) {
			$last->addClass('last');
		}
	}
}


?>