<?php

class THtmlFlashPlayer extends TCompositeControl {

	public function __construct($params = array()) {
		parent::__construct();

		$this->setAutoPlay(false);
		$this->setInitialScale('scale');
		$this->setLoop(false);
		$this->setAutoRewind(true);
		$this->setControlsOverVideo('ease');
		$this->setShowFullScreenButton(false);
		$this->setShowMenu(false);
		$this->setAutoBuffering(false);

		$this->parseParams($params);
	}

	public function init() {

		$this->swfobject = $this->children[] = zing::create('THtmlScript', array('src' => "/Zing/Assets/Scripts/swfobject/src/swfobject.js"));
		$this->containerDiv = $this->children[] = zing::create('THtmlDiv', array('id' => 'videoContainer-' . zing::createControlId(), 'hideWhenEmpty' => false, 'collapse' => false));
		$this->containerDiv->children[] = zing::create('THtmlDiv', array('tag' => 'h2', 'innerText' => 'Embedded Video Clips'));
		$this->containerDiv->children[] = zing::create('THtmlDiv', array('tag' => 'p', 'innerText' => 'You are missing the necessary components to allow for embedded video to play on this website.'));
		$this->inlineScript = $this->children[] = zing::create('THtmlScriptInline');

		foreach ($this->children as $child) {
			$child->doStatesUntil('preInit');
		}

		parent::init();
	}

	private $height = 240;

	public function setHeight($h) {
		$this->height = $h;
	}

	public function getHeight() {
		return $this->height;
	}

	private $width = 320;

	public function setWidth($w) {
		$this->width = $w;
	}

	public function getWidth() {
		return $this->width;
	}

	private $config;

	public function setVideoFile($vf) {
		$this->config['videoFile'] = $vf;
	}

	public function setAutoPlay($auto) {
		$this->config['autoPlay'] = zing::evaluateAsBoolean($auto);
	}

	public function setSplashImageFile($sf) {
		$this->config['splashImageFile'] = $sf;
	}

	public function setInitialScale($scale) {
		$this->config['initialScale'] = $scale;
	}

	public function setLoop($loop) {
		$this->config['loop'] = zing::evaluateAsBoolean($loop);
	}

	public function setAutoRewind($rewind) {
		$this->config['autoRewind'] = zing::evaluateAsBoolean($rewind);
	}

	public function setControlsOverVideo($controls) {
		$this->config['controlsOverVideo'] = $controls;
	}

	public function setShowFullScreenButton($full) {
		$this->config['showFullScreenButton'] = zing::evaluateAsBoolean($full);
	}

	public function setShowMenu($menu) {
		$this->config['showMenu'] = zing::evaluateAsBoolean($menu);
	}

	public function setAutoBuffering($ab) {
		$this->config['autoBuffering'] = zing::evaluateAsBoolean($ab);
	}

	public function render() {

		$script = 'swfobject.embedSWF("/Zing/Assets/Scripts/flowplayer/FlowPlayerDark.swf"';
		$script .= ',"' . $this->containerDiv->getId() . '"';
		$script .= ',"' . $this->getWidth() . '"';
		$script .= ',"' . $this->getHeight() . '"';
		$script .= ',"9.0.0"';
		$script .= ',"/Zing/Assets/Scripts/swfobject/expressInstall.swf"';
		if (count($this->config)) {
			$script .= ', { config: "{';
			$i = 0;
			foreach ($this->config as $name => $value) {
				if ($i++) {
					$script .= ', ';
				}
				$script .= $name . ': ';
				if (is_string($value)) {
					$script .= '\'' . $value . '\'';
				} elseif (is_bool($value)) {
					$script .= $value ? 'true' : 'false';
				} else {
					$script .= $value;
				}
			}
			$script .= '}"}';
		}

		$script .= ');';
		$this->inlineScript->setInnerText($script);
		parent::render();
	}

}

?>
