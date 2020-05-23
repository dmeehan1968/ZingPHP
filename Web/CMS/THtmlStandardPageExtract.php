<?php

class THtmlStandardPageExtract extends TModule {

	private $uri;

	public function setUri($uri) {
		$this->uri = $uri;
	}

	public function hasUri() {
		return isset($this->uri);
	}

	public function getUri() {
		return $this->uri;
	}

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

		$page = CmsPage::FindOnePublishedByUri($sess->parameters->pdo, $this->getUri());

		if (! isset($page)) {
			$page = new CmsPage($sess->parameters->pdo);
			$page->body = 'Error: Page Extract Not Found';
			$this->setBoundObject($page);
		}
		$this->setBoundObject($page);

		parent::load();
	}

}


?>
