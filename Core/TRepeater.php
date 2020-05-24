<?php

class TRepeater extends TCompositeControl {

	private	$iterations = 0;
	private $extent = 0;

	public function addIteration() {
		$this->iterations++;
	}

	public function getIterations() {
		return $this->iterations;
	}

	public function resetIterations() {
		$this->iterations = 0;
	}

	public function setExtent($extent) {
		$this->extent = $extent;
	}

	public function getExtent() {
		return $this->extent;
	}

	public $ownBoundObject;

	public function preRender() {
		/**
		 * If we start with our own bound object (rather than inherited from
		 * a container), then we record it here so that it can be restored
		 * after the repeat is complete
		 */
		if ($this->hasOwnBoundObject()) {
			$this->ownBoundObject = $this->getBoundObject();
		}
		parent::preRender();
	}

	public function render() {

		if ($this->hasBoundObject()) {

			$boundObject = $this->getBoundObject();
			$this->setExtent(is_array($boundObject) ? count($boundObject) : 0);

			foreach ($boundObject as $element) {
				foreach ($this->children as $child) {
					$child->setBoundObject($element);
					$child->preRender();
					if ($this->hasOnRepeat()) {
						$this->fireEvent($this->getOnRepeat(), $child);
					}
				}

				$this->addIteration();

				parent::render();
			}

			/**
			 * Restore the original bound object (or reset if none)
			 */
			$this->setBoundObject($this->ownBoundObject);
		}
	}

	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$object = $this->getBoundObject();
			$property = $this->getBoundProperty();
			$value = TControl::resolveBoundValue($object, $property);
			$this->setBoundObject($value);
		} else {
			parent::bind();
		}
	}

	private $onRepeat;

	public function setOnRepeat($repeat) {
		$this->onRepeat = $repeat;
	}

	public function getOnRepeat() {
		return $this->onRepeat;
	}

	public function hasOnRepeat() {
		return isset($this->onRepeat);
	}
}

?>
