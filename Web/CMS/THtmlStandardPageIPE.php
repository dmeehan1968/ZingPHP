<?php

class THtmlStandardPageIPE extends TModule {

	public function preInit() {
		parent::preInit();
		
		$sess = TSession::getInstance();
		$timeout = $sess->parameters['cms.standardpage.cache.timeout'];
		if (empty($timeout)) {
			$timeout = -1;
		}
		$sess->app->page->setCacheTimeout($timeout);
	}

	public function load() {
	
		$sess = TSession::getInstance();
		
		$page = CmsPage::FindOnePublishedByUri($sess->parameters->pdo, $sess->app->request->uri);

		$this->setBoundObject($page);
		
		parent::load();
	}
	
	public function render() {
	
		if (is_null($this->getBoundObject())) {
			$this->children->deleteAll();
			
			$content = $this->children[] = zing::create('StaticOrNotFound');
			$content->doStatesUntil('render');
		} else {
			parent::render();
		}
	}
	
	public function setBodyId($control, $params) {
		$page = $this->getBoundObject();
		if (!empty($page)) {
			$control->setId(zing::urltext($page->title));
			
			$classes = preg_split('/\//', $page->uri);
			foreach ($classes as $class) {
				if (strlen($class)) {
					$control->addClass($class);
				}
			}			
		}
	}
	
	public function setEditLink($control, $params) {
		$page = $this->getBoundObject();
		if (!empty($page)) {
			$control->setPage_Id($page->id);
		}
	}

	public function insertPageTitle($control, $params) {
		$page = $this->getBoundObject();
		$control->setTitle($page->title);
	}
}


?>