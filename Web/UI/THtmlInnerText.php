<?php

class THtmlInnerText extends TControl {

	private	$value;

	public function setValue($value, $forceEmpty = false) {
		if (!$forceEmpty && empty($value) && $value !== 0) {
			unset($this->value);
		} else {
			$this->value = $value;
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function hasValue() {
		return isset($this->value);
	}

	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$property = $this->getBoundProperty();
			$object = $this->getBoundObject();
			$value = TControl::resolveBoundValue($object, $property);

			$this->setValue($value);
		}

		parent::bind();
	}

	private $callbacks;

	public function setCallbacks($callbacks) {
		if (is_array($callbacks)) {
			$this->callbacks = implode(',', $callbacks);
		} else {
			$this->callbacks = $callbacks;
		}
	}

	public function getCallbacks() {
		return explode(',',$this->callbacks);
	}

	public function hasCallbacks() {
		return isset($this->callbacks);
	}

	public function render() {
		parent::render();

		if ($this->getVisible()) {
			if (is_null($this->value)) {
				// do nothing
			} else if (is_object($this->value)) {
				echo '{'.get_class($this->value).'}';
			} else {
				if (preg_match('/^@@(.*)@@$/sm', $this->value, $matches) == 1) {
					echo $matches[1];
				} else {
					if ($this->hasCallbacks()) {
						$value = $this->value;
						foreach ($this->getCallbacks() as $callback) {
							$value = call_user_func_array(explode('::',$callback), array($value));
						}
						echo $value;
					} else {
						// DJM 2016-04-19: Added character set to ensure correct conversion
						// and avoid blank output
						echo htmlentities($this->value, ENT_QUOTES | ENT_SUBSTITUTE, 'iso-8859-1');
					}
				}
			}
		}
	}

}

?>
