<?php

class THtmlYahooTabView extends TCompositeControl {

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
	public function loadComplete() {

		$element = $this->getTopControl()->getDescendantById($this->getElementId());
		$element->addClass('yui-navset');
		foreach ($element->children as $index => $child) {
			if ($child instanceof THtmlControl && $child->getTag() == 'ul') {
				$child->addClass('yui-nav zing-nav-horizontal');
				foreach ($child->children as $li) {
					if ($li instanceof THtmlControl && $li->getTag() == 'li') {
						$li->addClass('selected');
						break;
					}
				}
			}
			if ($child instanceof THtmlControl && $child->getTag() == 'div') {
				$br = $element->children->insertBefore($index, zing::create('THtmlBr'));
				$br->doStatesUntil('load');
				$child->addClass('yui-content');
			}
		}

		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/yahoo-dom-event/yahoo-dom-event.js'));
		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/element/element-beta.js'));
		$new[] = $this->children[] = zing::create('THtmlScript', array('src' => '/Zing/Assets/Scripts/yui/tabview/tabview.js'));

		$new[] = $this->children[] = zing::create('THtmlScript', array('innerText' => '

		YAHOO.util.Event.onContentReady("' . $this->getElementId() . '", function() {
				var oMenu = new YAHOO.widget.TabView("' . $this->getElementId() . '" );
		});

	'));

		foreach ($new as $child) {
			$child->doStatesUntil('Load');
		}
		parent::loadComplete();
	}


}

?>
