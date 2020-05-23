<?php

class THtmlYahooDropDownMenu extends TCompositeControl {

	private $elementId;

	public function setElementId($id) {
		$this->elementId = $id;
	}

	public function getElementId() {
		return $this->elementId;
	}

	public function hasElementId() {
		return isset($this->elementId);
	}

	public function updateMenuItems($ul, $root = true) {
		foreach ($ul->children as $li) {
			if ($li instanceof THtmlControl && $li->getTag() == 'li') {
				foreach ($li->children as $index => $child) {
					if ($child instanceof THtmlControl && $child->getTag() == 'ul') {
						$this->insertYuiModule($li, $index);
					}
				}
			}
		}
	}

	public function insertYuiModule($parent, $menuIndex) {
		if (! is_null($menuIndex)) {
			$menu = clone $parent->children[$menuIndex];
			$div = $parent->children[$menuIndex] = zing::create('THtmlDiv');
			$div->children[] = $menu;
			$parent = $div;
		}

		$children = clone $parent->children;
		$parent->children->deleteAll();
		$bd = $parent->children[] = zing::create('THtmlDiv', array('class' => 'bd'));
		foreach ($children as $child) {
			$bd->children[] = $child;
			if ($child instanceof THtmlControl && $child->getTag() == 'ul') {
				$this->updateMenuItems($child, is_null($menuIndex) ? true : false);
			}
		}
	}

	public function loadComplete() {

		$element = $this->getDescendantById($this->getElementId());
		$this->insertYuiModule($element, null);
		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/yahoo-dom-event/yahoo-dom-event.js'));
		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/container/container_core.js'));
		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/menu/menu.js'));
		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/animation/animation.js'));

		$new[] = $this->children[] = zing::create('THtmlScript', array('innerText' => '

		YAHOO.util.Event.onContentReady("' . $this->getElementId() . '", function() {
				var oMenu = new YAHOO.widget.MenuBar("' . $this->getElementId() . '", {
							autosubmenudisplay: true,
							submenuhidedelay: 250,
							lazyload: true } );
				oMenu.render();
				oMenu.show();

		});
	'));

		foreach ($new as $child) {
			$child->doStatesUntil('Load');
		}
		parent::loadComplete();
	}

}

?>
