<?php

class THtmlBodyPlaceholder extends THtmlControl {

	public function init() {
		parent::init();
		$this->setTag('body');
	}

	public function render() {
		$top = $this->getTopControl();
		$controls = $this->getDescendantsByClass('THtmlBodyComponent');

		foreach ($controls as $control) {
			if ($control->isVisible()) {
				foreach ($control->attributes as $key => $attr) {
					switch ($key) {
					case 'class':
						$this->addClass($attr);
						break;
					default:
						$this->attributes[$key] = $attr;
						break;
					}
				}

				foreach ($control->children as $key => $child) {
					$this->children[$key] = $child;
				}
			}
		}

		parent::render();
	}

}

?>
