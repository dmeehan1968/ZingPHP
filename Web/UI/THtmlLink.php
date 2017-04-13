<?php

class THtmlLink extends THtmlControl {

	public function __construct($params = array()) {
		parent::__construct($params);
		
		$this->setTag('a');
	}
	
	public function setHref($href) {
		if (empty($href)) {
			unset($this->attributes['href']);
		} else {
			$this->attributes['href'] = $href;
		}
	}
	
	public function getHref() {
		return $this->attributes['href'];
	}
	
	public function hasHref() {
		return isset($this->attributes['href']);
	}
	
	public function setTitle($title) {
		$this->attributes['title'] = $title;
	}
	
	public function getTitle() {
		return $this->attributes['title'];
	}
	
	public function hasTitle() {
		return isset($this->attributes['title']);
	}
	
	private $module;
	
	public function setModule($module) {
		$this->module = $module;
	}
	
	public function getModule() {
		return $this->module;
	}
	
	public function hasModule() {
		return isset($this->module);
	}
	
	public function setTarget($target) {
		$this->attributes['target'] = $target;
	}
	
	public function getTarget() {
		return $this->attributes['target'];
	}
	
	public function hasTarget() {
		return isset($this->attributes['target']);
	}
	
	private $params = array();
	
	public function __call($name, $params) {
		$name = strtolower($name);
		
		if (substr($name,0,3) == 'set') {
			$name = substr($name,3);
			$this->params[$name] = array_shift($params);
		}	
	}
	
	private $queryParams = array();
	
	public function setQueryParams($params) {
		$this->queryParams = $params;
	}
	
	public function getQueryParams() {
		return $this->queryParams;
	}
	
	public function hasQueryParams() {
		return count($this->queryParams);
	}
	
	private $bookmark = null;
	
	public function setBookmark($bookmark) {
		$this->bookmark = $bookmark;
	}
	
	public function getBookmark() {
		return $this->bookmark;
	}
	
	public function hasBookmark() {
		return !is_null($this->bookmark);
	}
	
	public function prepareLink() {
		if ($this->getVisible()) {
			if ($this->hasModule()) {
				$sess = TSession::getInstance();
				
				$finalParams = array();
				foreach ($this->params as $name => $value) {
					list ($action, $value) = explode(':', $value);
					switch ($action) {
						case 'bind':
							if ($this->hasBoundObject()) {
								$value = urlencode($this->resolveBoundValue($this->getBoundObject(), $value));
							} else {
								$value = '';
							}
							break;
						default:
							$value = $action;
					}
					$finalParams[$name] = $value;
				}
				$this->setHref($sess->app->getModuleUri($this->getModule(), $finalParams, $this->queryParams, $this->bookmark));
				return;
			}
			if ($this->hasBoundUriProperty() && $this->hasBoundObject()) {
				$property = $this->getBoundUriProperty();
				$object = $this->getBoundObject();
				$value = TControl::resolveBoundValue($object, $property);
				$this->setHref($value);
			}
			if ($this->hasQueryParams()) {
				$this->setHref(http_build_query($this->getQueryParams(), 'var'));
			}
		}
	}
	
	private $boundUriProperty;
	
	public function setBoundUriProperty($prop) {
		$this->boundUriProperty = $prop;
	}
	
	public function getBoundUriProperty() {
		return $this->boundUriProperty;
	}
	
	public function hasBoundUriProperty() {
		return isset($this->boundUriProperty);
	}
	
	private $protocol;

	public function setProtocol($protocol) {
		$this->protocol = $protocol;
	}
	
	public function getProtocol() {
		return $this->protocol;
	}
	
	public function hasProtocol() {
		return !empty($this->protocol);
	}
	
	private $encodeInner = false;
	
	public function setEncodeInner($encode) {
		$this->encodeInner = zing::evaluateAsBoolean($encode);
	}
	
	public function getEncodeInner() {
		return $this->encodeInner;
	}
	
	public function render() {
		$this->prepareLink();
		
		if ($this->hasChildren() || count($this->attributes) || count($this->rawAttributes)) {
			if ($this->hasProtocol() && $this->hasHref()) {
				switch ($this->getProtocol()) {
					case 'mailto:':
						$this->setHref($this->getProtocol() . zing::encodeHex($this->getHref()));
						break;
					case 'http:':
						list($protocol, $uri) = explode('://', $this->getHref());
						if (empty($uri)) {
							$uri = $protocol;
							$protocol = $this->getProtocol();
						}
						$this->setHref($protocol . '//' . $uri);
						break;
				}
			}

			if ($this->getEncodeInner() && $this->hasInnerText()) {
				$this->setInnerText('@@' . zing::encodeEntity($this->getInnerText()) . '@@');
	 		}

			parent::render();
		}
	}
			
}

?>