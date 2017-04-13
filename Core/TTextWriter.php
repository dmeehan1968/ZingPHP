<?php

class TTextWriter implements IWriter {

	private $buffer;
	
	public function write($str) {
		$this->buffer .= $str;
	}
	
	public function writeLine($str='') {
		$this->buffer .= $str . "\n";
	}
	
	public function flush() {
		echo $this->buffer;
		$this->buffer = null;
	}
}


?>