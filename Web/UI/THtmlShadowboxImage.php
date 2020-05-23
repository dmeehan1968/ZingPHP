<?php

class THtmlShadowboxImage extends THtmlLink {

	private $thumbImage;
	private $gallery;

	public function __construct($params = array()) {

		$this->thumbImage = zing::create('THtmlImage');

		parent::__construct($params);

		$this->children[] = $this->thumbImage;
	}

	public function setThumb($thumb) {
		$this->thumbImage->setSrc($thumb);
	}

	public function getThumb() {
		return $this->thumbImage->getSrc();
	}

	public function hasThumb() {
		return $this->thumbImage->hasSrc();
	}

	public function setFull($full) {
		$this->setHref($full);
	}

	public function getFull() {
		return $this->getHref();
	}

	public function hasFull() {
		return $this->hasHref();
	}

	public function setGallery($gallery) {
		$this->gallery = $gallery;
		$this->attributes['rel'] = 'shadowbox[' . $gallery . ']';
	}

	public function getGallery() {
		return $this->gallery;
	}

	public function hasGallery() {
		return isset($this->gallery);
	}

	public function preRender() {
		if (! $this->hasGallery()) {
			$this->attributes['rel'] = 'shadowbox';
		}

		parent::preRender();
	}

}

?>
