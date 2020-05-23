<?php

class TRawOutput extends TControl {

	private $text;

	public function setInnerText($text) {
		$this->text = $text;
	}

	public function hasInnerText() {
		return isset($this->text);
	}

	public function getInnerText() {
		return $this->text;
	}

	public function bind() {
		if ($this->hasBoundObject() && $this->hasBoundProperty()) {
			$prop = $this->getBoundProperty();
			$output = $this->getBoundObject()->$prop;
			$this->setInnerText($output);
		}
	}

	public function render() {
		echo $this->getInnerText();
	}
}

?>
