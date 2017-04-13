<?php

class CmsSearch extends TModule {

	private $request = 'query';
	
	public function setRequest($request) {
		$this->request = $request;
	}
	
	public function hasRequest() {
		return isset($this->request);
	}
	
	public function getRequest() {
		return $this->request;
	}

	private $queryExpansion = false;
	
	public function setQueryExpansion($qe) {
		$this->queryExpansion = zing::evaluateAsBoolean($qe);
	}
	
	public function getQueryExpansion() {
		return $this->queryExpansion;
	}

	private $booleanMode = false;
	
	public function setBooleanMode($bm) {
		$this->booleanMode = zing::evaluateAsBoolean($bm);
	}
	
	public function getBooleanMode() {
		return $this->booleanMode;
	}
	
	public function load() {
		
		$sess = TSession::getInstance();
		
		$request = $this->getRequest();
		
		$this->searchQuery->setInnerText($sess->app->request->$request);
		
		$pages = CmsPage::findAllPublishedBySearch($sess->parameters->pdo, $sess->app->request->$request, $this->getBooleanMode(), $this->getQueryExpansion());
		
		if (count($pages)) {
			$this->searchFailed->setVisible(false);
			$this->searchResults->setBoundObject($pages);
		} else {
			$this->searchResults->setVisible(false);
		}
		
		parent::load();
	}
}

?>