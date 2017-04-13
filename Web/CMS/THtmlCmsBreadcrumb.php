<?php

class THtmlCmsBreadcrumb extends THtmlDiv {

	private $prefix;
	
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}
	
	public function getPrefix() {
		return $this->prefix;
	}
	
	public function hasPrefix() {
		return isset($this->prefix);
	}
	
	public function preRender() {
		$this->setTag('p');
		
		if ($this->hasPrefix()) {
			$this->children[] = zing::create('THtmlDiv', array('tag' => 'span', 'innerText' => $this->getPrefix()));
		}
		
		$sess = TSession::getInstance();
		if (isset($sess->app->request->breadcrumb)) {
			$uri = $sess->app->request->breadcrumb;
		} else {
			$uri = $sess->app->request->_modpath;
		}
		
		$links = array();
		
		$uriParts = explode('/', $uri);
		for ( ; !empty($uriParts) ; array_pop($uriParts)) {
			
			$alt = $sess->app->request['breadcrumb'.(count($uriParts)-1)];
			if (isset($alt)) {
				$page = CmsPage::findOnePublishedByUri($sess->parameters->pdo, $alt);
				$uri = implode('/', $uriParts);
			} else {
				$page = CmsPage::findOnePublishedByUri($sess->parameters->pdo, implode('/',$uriParts));
				$uri = $page->uri;
			}
			
			if ($page) {		
				$links[] = zing::create('THtmlLink', array('href' => $uri, 'innerText' => $page->title));
			}
		}
		
		$links[] = zing::create('THtmlLink', array('href' => '/', 'innerText' => 'Home'));
		
		for ($i=count($links)-1 ; $i >= 0 ; $i--) {
			if ($i < count($links)-1) {
				$this->children[] = zing::create('TPlainText', array('value' => ' > '));
			}
			$this->children[] = $links[$i];
		}
		
		foreach ($this->children as $child) {
			$child->doStatesUntil('loadComplete');
		}
		parent::preRender();
			
	}
}

?>