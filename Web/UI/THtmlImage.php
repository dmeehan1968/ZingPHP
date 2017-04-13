<?php

class THtmlImage extends THtmlControl {

	public function __construct($params = array()) {
		$this->setTag('img');
		parent::__construct($params);
	}
	
	public function setSrc($src) {
		$this->attributes['src'] = $src;
	}
	
	public function getSrc() {
		return $this->attributes['src'];
	}
	
	public function hasSrc() {
		return isset($this->attributes['src']);
	}
	
	public function setAlt($alt) {
		$this->attributes['alt'] = $alt;
	}
	
	public function getAlt() {
		return $this->attributes['alt'];
	}
	
	public function hasAlt() {
		return isset($this->attributes['alt']);
	}
	
	public function setHeight($height) {
		$this->attributes['height'] = $height;
	}
	
	public function getHeight() {
		return $this->attributes['height'];
	}
	
	public function hasHeight() {
		return isset($this->attributes['height']);
	}
	
	public function setWidth($width) {
		$this->attributes['width'] = $width;
	}
	
	public function getWidth() {
		return $this->attributes['width'];
	}
	
	public function hasWidth() {
		return isset($this->attributes['width']);
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

	/**
	 * $transform contains parameters to be inserted into the requested filename
	 * that cause the ImageView class to transform the original image. e.g.
	 *
	 * maxheight=300,maxwidth=300,quality=30
	 */
	private $transform;
	
	public function setTransform($transform) {
		$this->transform = $transform;
	}
	
	public function hasTransform() {
		return isset($this->transform);
	}
	
	public function getTransform() {
		return $this->transform;
	}
	
	public function bind() {
		if ($this->hasBoundObject() && $this->hasBoundProperty()) {
			$this->setBoundObject($this->resolveBoundValue($this->getBoundObject(), $this->getBoundProperty()));
		} else {
			parent::bind();
		}
	}
	
	private $altsrc;
	
	public function setAltSrc($src) {
		$this->altsrc = $src;
	}
	
	public function hasAltSrc() {
		return isset($this->altsrc);
	}
	
	public function getAltSrc() {
		return $this->altsrc;
	}
	
	public function onBindAttribute($attribute, $value) {
		$value = parent::onBindAttribute($attribute, $value);
		if ($attribute == 'src' && empty($value) && $this->hasAltSrc()) {
			$value = $this->getAltSrc();
		}
		if ($this->hasTransform()) {
			switch ($attribute) {
				case 'src':
					$parts = explode('.', $value);
					$ext = array_pop($parts);
					$value = implode('.', $parts) . '(' . $this->getTransform() . ').' . $ext; 
					break;
			}
		}
		return $value;
	}
	
}

?>