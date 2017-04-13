<?php

class THtmlDivNotify extends THtmlDiv {

	public function __construct($params = array()) {
		parent::__construct($params);
		$this->addClass('notification');
	}
	
	public function setNotification($class, $message) {
		if (is_bool($class)) {
			$class = $class ? 'notification-success' : 'notification-error';
		}
		
		$this->setClass('notification ' . $class);
		$this->setInnerText($message);
	}

	public function hasNotification() {
		return $this->hasInnerText();
	}
}


?>