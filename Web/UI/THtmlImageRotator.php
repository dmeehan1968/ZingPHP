<?php

class THtmlImageRotator extends THtmlDiv {

	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$this->setBoundObject($this->resolveBoundValue($this->getBoundObject(), $this->getBoundProperty()));
		}
	}

	private $settings;
	
	public function setSettings($settings) {
		$this->settings = $settings;
	}
	
	public function hasSettings() {
		return isset($this->settings);
	}
	
	public function getSettings() {
		return $this->settings;
	}
	
	private $base = '/images/';
	
	public function setBase($base) {
		if (empty($base)) {
			unset($this->base);
		} else {
			$this->base = $base;
		}
	}
	
	public function getBase() {
		return $this->base;
	}
	
	public function hasBase() {
		return isset($this->base);
	}
	
	public function preRender() {
		
		parent::preRender();
		
		$images = $this->getBoundObject();

		if (count($images)) {
		
			if (!$this->hasId()) {
				$this->setId('yui-sldshw-' . rand(1,1000));
			}
			$this->setClass('yui-sldshw-displayer imagerotator');
	
			foreach ($images as $index => $image) {
				$div = $this->children[] = zing::create('THtmlDiv', array('class' => 'yui-sldshw-frame' . ($index == 0 ? ' yui-sldshw-active' : '')));
				$filename = $image->filename;
				$parts = explode('.', $filename);
				$ext = array_pop($parts);
				$filename = implode('.', $parts) . ($this->hasSettings() ? '(' . $this->getSettings() . ')' : '') . '.' . $ext;
				$params['src'] = ($this->hasBase() ? $this->getBase() : '') . $filename;
				$params['alt'] = strlen($image->title) ? $image->title : 'No image title';
				$div->children[] = zing::create('THtmlImage', $params);
				$div->children[] = zing::create('TPlainText', array('value' => "\r\n"));
			}

			if (count($images) > 1) {			
				$script = 'YAHOO.util.Event.onContentReady("' . $this->getId() . '",
					function() {
						var oSS = new YAHOO.myowndb.slideshow("' . $this->getId() . '",
								{	effect: YAHOO.myowndb.slideshow.effects.fadeOut,
									interval: 4000
								} );
						oSS.loop();
					});';
				$yui = $this->children[] = zing::create('TYuiLoader');
				$yui->setAddModule('name: "slideshowCSS", type: "css", fullpath: "/Zing/Assets/Scripts/slideshow/slideshow.css"');
				$yui->setAddModule('name: "slideshow", type: "js", fullpath: "/Zing/Assets/Scripts/slideshow/slideshow.js", requires: [ "animation", "slideshowCSS" ]');
				$yui->setRequire('event, slideshow');
				$yui->setOnSuccess($script);
				$yui->doStatesUntil('postComplete');
			}
					
		}
		
		parent::preRender();
	}
}


?>