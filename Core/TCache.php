<?php

class TCache {

	private $path;
	private $object;
	private $username;
	
	private $modified;
	private $timeout;
	private $buffering = false;
	private $output;
		
	public function __construct($path, $object, $user = 'guest', $timeout = 3600) {

		if (substr($object,-1,1) == '/') {
			$object .= 'index';
		}
		
		if (substr($object,0,1) == '/') {
			$object = substr($object,1);
		}
	
		if (substr($path,-1,1) != '/') {
			$path .= '/';
		}
	
		$this->path = $path;
		$this->object = $object;
		$this->username = $user;
		$this->timeout = $timeout;

	}

	public function cachePath() {
		return $this->path . $this->username . '/' . $this->object . '.cache';
	}
	
	public function setCacheTimeout($timeout) {
		$this->timeout = $timeout;
	}
	
	public function getCacheTimeout() {
		return $this->timeout;
	}
	
	public function setUsername($username) {
		$this->username = $username;
	}
	
	public function getUsername() {
		return $this->username;
	}
	
	public function isExpired() {
		$modified = @filemtime($this->cachePath());
		if ($this->timeout == -1 || $modified === false || $modified < (time() - $this->timeout)) {
			return true;
		}
		return false;
	}
	
	public function startContent() {
		if ($timeout != -1) {
			header('Expires: ');
			header('Pragma: ');
			header('Cache-Control: ');
	
			ob_start();
			$this->buffering = true;
		}
	}
	
	public function getContent() {
		if (! $this->cacheData && file_exists($this->cachePath())) {
			$this->cacheData = unserialize(file_get_contents($this->cachePath()));
		}

		if ($this->cacheData) {
			return $this->cacheData->recreate();
		}

		return $this->output;
	}

	public function destroyCache() {
		if (file_exists($this->cachePath())) {
			unlink($this->cachePath());
		}
	}
			
	public function saveContent() {
		
		if ($this->isExpired()) {
			$this->destroyCache();
		}			
		
		if ($this->buffering) {
			$this->output = ob_get_contents();
			ob_end_clean();
			$this->buffering = false;
		}

		if ($this->output && ! file_exists($this->cachePath()) && $this->timeout != -1) {
			$this->_saveContent();
		}
	}

	protected function _saveContent() {
		$dirs = explode('/', $this->cachePath());
		array_pop($dirs);		// remove filename
		$orig = $dirs;
		
		while (count($dirs)) {
			$path = '/' . implode('/',$dirs);
			if (!is_file($path) && is_dir($path)) {
				break;
			}
			array_pop($dirs);
		}
		
		while (count($dirs) < count($orig)) {
			$dirs[] = $orig[count($dirs)];
			$path = implode('/', $dirs);
			mkdir($path);
			chmod($path, 0777);
		}
		
		$this->cacheData = new TCacheData(headers_list(), $this->output);				
 		if ($this->timeout != -1) {
	 		$this->cacheData->commit($this->cachePath());
		}
	}
}


class TCacheData {
	public $headers;
	public $date;
	public $etag;
	public $committed = false;
	public $content;
				
	public function __construct($headers = array(), $content = '') {
		$this->headers = $headers;
		$this->content = $content;
		$this->date = gmdate('D, d M Y H:i:s', time()) . ' GMT';
		$this->etag = md5($this->content);
	}
	
	public function recreate() {
	
		$sess = TSession::getInstance();
		
		$send = true;
		
		if ($sess->app->server->http_if_none_match && substr($sess->app->server->http_if_none_match,1,-1) == $this->etag) {
			$send = false;
		}

		if ($sess->app->server->http_if_modified_since && strtotime($sess->app->server->http_if_modified_since) >= strtotime($this->date)) {
			$send = false;
		}
				
		if (!$send) {
			header('HTTP/1.1 304 Not Modified');
		}
	
		foreach ($this->headers as $header) {
			header($header);
		}

		header('Zing-Cache: ' . ($this->committed ? $this->date : 'false'));
		header('Last-Modified: ' . $this->date);
		header('Etag: "' . $this->etag . '"');

		if ($send) {
			return $this->content;
		}
		
		return null;
	}

	public function commit($path) {	
		$this->committed = true;	
		file_put_contents($path, serialize($this), LOCK_EX);
		chmod($path, 0777);
		$this->committed = false;	
	}
}



?>