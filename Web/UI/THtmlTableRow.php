<?php

class THtmlTableRow extends THtmlControl {

	public function __construct($params = array()) {
		parent::__construct($params);
		
		$this->setTag('tr');
	}

}


?>