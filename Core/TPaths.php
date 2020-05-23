<?php

class TPaths extends TRegistry {

	public function __set($name, $value) {
		if (substr($value,-1,1) != '/') {
			$value .= '/';
		}

		if (substr($value,0,1) != '/') {
			$value = $this->base . $value;
		}

		parent::__set($name, $value);
	}
}

?>
