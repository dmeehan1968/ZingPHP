<?php

class TYuiMenu extends THtmlDiv {

	public function getYuiMenuClass() {
		return 'yuimenu';
	}
	
	public function getYuiMenuObject() {
		return 'Menu';
	}
	
	public function getYuiMenuItemType() {
		return 'TYuiMenuItem';
	}
	
	private $show = false;
	
	public function setShow($show) {
		$this->show = $show;
	}
	
	public function getShow() {
		return $this->show;
	}
		
	public function isSubMenu() {
		
		$control = $this;
		while ($control instanceof IContained && $control = $control->getContainer()) {
			if ($control instanceof TYuiMenu) {
				return true;
			}
		}
		return false;
	}
	
	public function preRender() {
		
		if ($this->hasPermission()) {
				
			$this->addClass($this->getYuiMenuClass());
			
			$children = clone $this->children;
			$this->children->deleteAll();
		
			if (! $this->isSubMenu()) {
				$ID = $this->getId();
				$CLASS = $this->getYuiMenuClass();
				$OBJECT = $this->getYuiMenuObject();
				$FUNCTION = $ID . '_' . $CLASS . '_create';
				$SHOW = $this->getShow() ? 'oMenu.show();' : '';
				$script = <<<EOT

			YAHOO.util.Event.onContentReady("$ID",
				function() {
					var oMenu = new YAHOO.widget.${OBJECT}("$ID");
					oMenu.render();
					${SHOW}
				}
			);
EOT;
	
				$this->children[] = zing::create('TYuiLoader', array('require' => 'menu', 'onSuccess' => $script));
			}
	
			$bd = $this->children[] = zing::create('THtmlDiv', array('class' => 'bd'));
			$ul = $bd->children[] = zing::create('THtmlDiv', array('tag' => 'ul', 'class' => 'first-of-type'));
			
			foreach ($this->children as $child) {
				$child->doStatesUntil('postComplete');
			}
	
			$ul->children = $children;		
			foreach ($ul->children as $child) {
				$class = $this->getYuiMenuItemType();
				if ($child instanceof $class) {
					$child->addClass('first-of-type');
					break;
				}
			}
		
			parent::preRender();
		}
	}

}

?>