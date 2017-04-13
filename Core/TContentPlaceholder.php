<?php

class TContentPlaceholder extends TControl implements IContainer {

	private	$content;
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getContent() {
		return $this->content;
	}

	public function getLayoutContent() {
		$control = $this;
		while ($control instanceof IContained && $control = $control->getContainer()) {
			if ($control instanceof TLayout) {
				return $control->getContent();
			}
		}
	}
		
	public function getChildren() {
		return array($this->getContent());
	}
	
	public function getDescendantById($id) {
		$content = $this->getContent();
		if ($content->getId() == $id) {
			return $content;
		}
		
		return $content->getDescendantById($id);
	}
	
	public function getDescendantsByClass($class) {
		$content = $this->getContent();
		$array = array();
		if ($content instanceof $class) {
			$array[] = $content;
		}
		return array_merge($array, $content->getDescendantsByClass($class));
	}


	public function preInit() {
		$this->setContent($this->getLayoutContent());
		
		parent::preInit();
		$this->getContent()->preInit();
	}

	public function init() {
		parent::init();
		$this->getContent()->init();
	}

	public function initComplete() {
		parent::initComplete();
		$this->getContent()->initComplete();
	}

	public function auth() {
		parent::auth();
		$this->getContent()->auth();
	}
	
	public function preLoad() {
		parent::preLoad();
		$this->getContent()->preLoad();
	}

	public function load() {
		parent::load();
		$this->getContent()->load();
	}

	public function loadComplete() {
		parent::loadComplete();
		$this->getContent()->loadComplete();
	}

	public function prePost() {
		parent::prePost();
		$this->getContent()->prePost();
	}

	public function post() {
		parent::post();
		$this->getContent()->post();
	}

	public function postComplete() {
		parent::postComplete();
		$this->getContent()->postComplete();
	}

	public function preRender() {
		parent::preRender();
		$this->getContent()->preRender();
	}

	public function render() {
		parent::render();
		$this->getContent()->render();
	}

	public function renderComplete() {
		parent::renderComplete();
		$this->getContent()->renderComplete();
	}


}

?>