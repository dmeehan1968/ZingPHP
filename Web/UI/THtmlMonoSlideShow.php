<?php

class THtmlMonoSlideShow extends THtmlDiv {

	public function init() {

		$this->addClass('monoslideshow');

		if (! $this->hasId()) {
			$this->setId('monoslideshow-' . zing::createControlId());
		}
		$this->swfobject = $this->children[] = zing::create('THtmlScript', array('src' => "/Zing/Assets/Scripts/swfobject/src/swfobject.js"));
		$this->containerDiv = $this->children[] = zing::create('THtmlDiv', array('id' => 'monoslideshow-' . zing::createControlId(), 'class' => 'monoslideshow-container'));
		$this->inlineScript = $this->children[] = zing::create('THtmlScriptInline');
		$this->imageControl = $this->containerDiv->children[] = zing::create('THtmlImage', array('visible' => false));
		$this->containerDiv->children[] = zing::create('THtmlDiv', array('tag' => 'p', 'innerText' => 'Image slideshow not supported by your brower.  Requires Flash Player 7+ and Javascript enabled.', 'style' => 'width: ' . $this->getWidth() . 'px'));
		$p = $this->containerDiv->children[] = zing::create('THtmlDiv', array('tag' => 'p'));
		$p->children[] = zing::create('THtmlLink', array('href' => 'http://get.adobe.com/flashplayer/', 'innerText' => 'Get Adobe Flash Player'));

		foreach ($this->children as $child) {
			$child->doStatesUntil('preInit');
		}

		parent::init();
	}

	private $height = 320;

	public function setHeight($height) {
		$this->height = $height;
	}

	public function getHeight() {
		return $this->height;
	}

	private $width = 240;

	public function setWidth($width) {
		$this->width = $width;
	}

	public function getWidth() {
		return $this->width;
	}

	private $image;

	public function setImage($image) {
		$this->image = $image;
	}

	public function getImage() {
		return $this->image;
	}

	public function hasImage() {
		return isset($this->image);
	}

	private $xml;

	public function setXml($xml) {
		$this->xml = $xml;
	}

	public function getXml() {
		return $this->xml;
	}

	public function render() {

		$xml = $this->onBindAttribute('xml', $this->getXml());
		$script = "\n" . 'swfobject.embedSWF("/Zing/Assets/Scripts/MonoSlideShow/monoslideshow.swf"';
		$script .= ',"' . $this->containerDiv->getId() . '"';
		$script .= ',"' . $this->getWidth() . '"';
		$script .= ',"' . $this->getHeight() . '"';
		$script .= ',"7"';
		$script .= ',"/Zing/Assets/Scripts/swfobject/expressInstall.swf"';
		$script .= ',{ dataFile: "'. $xml . '", showLogo: false }';
		$script .= ');';
		$this->inlineScript->setInnerText($script);

		if ($this->hasImage()) {
			$image = $this->onBindAttribute('image', $this->getImage());
//DME 20/02/2013 - Next line is a slight bodge as resolveBoundValue isn't properly returning array elements
			if ($image instanceof TObjectCollection)  $image = $image[0];
			$this->imageControl->setVisible(true);
			$this->imageControl->setSrc($image->filename);
			$this->imageControl->setAlt($image->author . ' ' . $image->title);
			$this->imageControl->setTransform('maxheight=' . $this->getHeight() . ',maxwidth=' . $this->getWidth() . ',quality=30');
			//comment out so that dimensions are not forced square
			//$this->imageControl->setHeight($this->getHeight());
			$this->imageControl->setWidth($this->getWidth());
		}

		parent::render();
	}

}

?>
