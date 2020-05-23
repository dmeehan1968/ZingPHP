<?php

class THtmlBr extends THtmlDiv {

	function init() {
		parent::init();
		$this->setTag('br');
		$this->setHideWhenEmpty(false);
	}

}

?>
