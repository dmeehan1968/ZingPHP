<?php

class TCompositeControl extends TControl implements IObserver, IContainer {

	public $children;
	
	function __construct($params = array()) {
		$this->children = new TRegistry;
		$this->children->observers[] = $this;

		parent::__construct($params);
	}		

	public function observedEvent($object, $event, $params = array()) {
		if ($event == TRegistry::EVT_ADD) {
			$child = $params['after'];				// after = child control
			$params['name'] = $child->getId();	// ensure the control name is correct in registry
			$child->setContainer($this);			// add back reference to this in child control
		}
	}

	public function getChildren() {
		return $this->children;
	}
	
	public function hasChildren() {
		return count($this->children) ? true : false;
	}
	
	public function getDescendantById($id) {
		if (isset($this->children[$id])) {
			return $this->children[$id];
		}
		
		foreach ($this->children as $child) {
			if ($child instanceof IContainer) {
				if ($desc = $child->getDescendantById($id)) {
					return $desc;
				}
			}
		}
		
		return null;
	}
	
	public function getDescendantsByClass($class) {
		$array = array();
		foreach ($this->children as $child) {
			if ($child instanceof $class) {
				$array[] = $child;
			}
			if ($child instanceof IContainer) {
				$array = array_merge($array, $child->getDescendantsByClass($class));
			}
		}
		
		return $array;
	}
	
	public function preInit() { 
		parent::preInit();
		foreach ($this->children as $child) {
			$child->preInit();
		}
	}
	
	public function init() {
		parent::init();
		foreach ($this->children as $child) {
			$child->init();
		}
	}
	
	public function initComplete() {
		parent::initComplete();
		foreach ($this->children as $child) {
			$child->initComplete();
		}
	}

	public function auth() {
		parent::auth();
		foreach ($this->children as $child) {
			$child->auth();
		}
	}
	
	public function preLoad() {
		parent::preLoad();
		foreach ($this->children as $child) {
			$child->preLoad();
		}
	}
	public function load() {
		parent::load();
		foreach ($this->children as $child) {
			$child->load();
		}
	}
	public function loadComplete() {
		parent::loadComplete();
		foreach ($this->children as $child) {
			$child->loadComplete();
		}
	}
	public function prePost() {
		parent::prePost();
		foreach ($this->children as $child) {
			$child->prePost();
		}
	}
	public function post() {
		parent::post();
		foreach ($this->children as $child) {
			$child->post();
		}
	}
	public function postComplete() {
		parent::postComplete();
		foreach ($this->children as $child) {
			$child->postComplete();
		}
	}
	public function preRender() {
		parent::preRender();
		foreach ($this->children as $child) {
			$child->preRender();
		}
	}	
	public function render() {
		parent::render();
		$this->renderChildren();
	}
	
	public function renderChildren() {
		if ($this->getVisible() && $this->hasPermission()) {
			foreach ($this->children as $child) {
				$child->render();
			}
		}
	}
	
	public function renderComplete() {
		parent::renderComplete();
		foreach ($this->children as $child) {
			$child->renderComplete();
		}
	}
	
	public function __get($name) {
		if (isset($this->children[$name])) {
			return $this->children[$name];
		}
		
		foreach ($this->children as $child) {
			$grandchild = $child->$name;
			if (isset($grandchild)) {
				return $grandchild;
			}
		}
	}

	public function getChildIds() {
		$array = array();
		
		foreach ($this->children as $id => $notused) {
			$array[] = $id;
		}
		
		return $array;
	}
	
	public function isVisible() {
		if ($this->hasContainer() && $this->getContainer() instanceof IVisibility && ! $this->getContainer()->isVisible()) {
			return false;
		}
		
		return $this->getVisible();
	}
	
}

?>