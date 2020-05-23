<?php

class TCachedLayout extends TLayout {

	private $cache;

	public function setCacheTimeout($timeout) {
		$this->cache->setCacheTimeout($timeout);
	}

	public function getCacheTimeout() {
		return $this->cache->getCacheTimeout();
	}

	public function preInit() {
		$sess = TSession::getInstance();
		$this->cache = new TCache($sess->paths->cache, $sess->app->request->_modpath);
		parent::preInit();
	}

	public function auth() {
		parent::auth();

		if (! $this->authManager->isGuest()) {
			$this->cache->setUsername($this->authManager->getUsername());
		}
	}

	public function preLoad() {
		$sess = TSession::getInstance();
		if ($sess->app->server->http_cache_control == 'no-cache' ||
			$sess->app->server->http_cache_control == 'max-age=0') {
			// force cache reload
			$this->cache->destroyCache();
		}

		if ($this->cache->isExpired()) {
			parent::preLoad();
		}
	}

	public function load() {
		if ($this->cache->isExpired()) {
			parent::load();
		}
	}

	public function loadComplete() {
		if ($this->cache->isExpired()) {
			parent::loadComplete();
		}
	}

	public function prePost() {
		if ($this->cache->isExpired()) {
			parent::prePost();
		}
	}

	public function post() {
		if ($this->cache->isExpired()) {
			parent::post();
		}
	}

	public function postComplete() {
		if ($this->cache->isExpired()) {
			parent::postComplete();
		}
	}

	public function preRender() {
		if ($this->cache->isExpired()) {
			parent::preRender();
		}
	}

	public function render() {

		if ($this->cache->isExpired()) {
			echo $this->cache->startContent();
			parent::render();
		}
	}

	public function renderComplete() {
		parent::renderComplete();

		$this->cache->saveContent();

		echo $this->cache->getContent();
	}


}

?>
