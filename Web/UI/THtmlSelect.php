<?php

class THtmlSelect extends THtmlControl {


	public function preRender() {
		parent::preRender();

		if ($this->getTag() == 'div') {
			$this->setTag('select');
		}
		if (!$this->hasName()) {
			$this->setName($this->getId());
		}
	}

	public function render() {

		if ($this->getMultiple() && strstr($this->getName(), '[]') === false) {
			$this->setName($this->getName() . '[]');
		}

		if ($this->hasBoundObject() && $this->hasBoundProperty()) {
			list($value, $text) = explode('|', $this->getBoundProperty());
			if (empty($value) || empty($text)) {
				throw new Exception('Invalid BoundProperty specification: \''.$this->getBoundProperty().'\' for THtmlSelect');
			}
			foreach ($this->getBoundObject() as $object) {
				$child = $this->children[] = zing::create('THtmlSelectOption', array('value' => $object->$value, 'innerText' => $object->$text));
				$child->setSelected(null);
				if ($this->hasSelected()) {
					$select = $this->getSelected();
					if ((method_exists($select, 'isEqual') && $select->isEqual($object))
							|| $select === $object) {
						$child->setSelected();
					}
				}
				$child->doStatesUntil('preRender');
			}
		}
		parent::render();
	}

	private $selected;

	public function setSelected($selected) {
		$this->selected = $selected;
	}

	public function getSelected() {
		return $this->selected;
	}

	public function hasSelected() {
		return isset($this->selected);
	}

	public function hasInnerContent() {
		return true;
	}

	public function setMultiple($multi) {
		$multi = zing::evaluateAsBoolean($multi);
		if ($multi) {
			$this->attributes['multiple'] = 1;
		} else {
			unset($this->attributes['multiple']);
		}
	}

	public function hasMultiple() {
		return isset($this->attributes['multiple']);
	}

	public function getMultiple() {
		return $this->hasMultiple();
	}

	public function setSize($size) {
		$this->attributes['size'] = $size;
	}

	public function getSize() {
		return $this->attributes['size'];
	}

	public function hasSize() {
		return isset($this->attributes['size']);
	}

	public function setDisabled($disabled) {
		if ($disabled) {
			$this->attributes['disabled'] = $disabled;
		} else {
			unset($this->attributes['disabled']);
		}
	}

	public function getDisabled() {
		return $this->attributes['disabled'];
	}

	public function hasDisabled() {
		return isset($this->attributes['disabled']);
	}

	public function post() {

		parent::post();

		$sess = TSession::getInstance();
		if ($this->hasId() && isset($sess->app->post[$this->getId()])) {
			$posted = $sess->app->post[$this->getId()];
			list($value, $text) = explode('|', $this->getBoundProperty());
			foreach ($this->getBoundObject() as $object) {
				if ($object->$value == $posted) {
					$this->setSelected($object);
				}
			}
		}
	}

}

?>
