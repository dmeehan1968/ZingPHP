<?php

class THtmlEmbedWMV extends TCompositeControl {

	private $filename;
	
	public function setFilename($fn) {
		$this->filename = $fn;
	}
	
	public function getFilename() {
		return $this->filename;
	}
	
	public function hasFilename() {
		return !empty($this->filename);
	}
	
	private $height;
	
	public function setHeight($h) {
		$this->height = $h;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function hasHeight() {
		return !empty($this->height);
	}
	
	private $width;
	
	public function setWidth($w) {
		$this->width = $w;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function hasWidth() {
		return !empty($this->width);
	}
	
	public function render() {
	
		if ($this->hasFilename()) {
		
			$this->children->deleteAll();
			
			$object = $this->children[] = zing::create('THtmlObject',
							array(	'classid' => 'clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B',
									'standby' => 'Loading Video...',
									'codebase' => 'http://www.apple.com/qtactivex/qtplugin.cab'));
			$object->children[] = zing::create('THtmlParam', array('name' => 'src', 'value' => $this->getFilename()));
			$object->children[] = zing::create('THtmlParam', array('name' => 'autoplay', 'value' => false));
			$object->children[] = zing::create('THtmlParam', array('name' => 'controller', 'value' => true));
			$embed = $object->children[] = zing::create('THtmlEmbed', array('src' => $this->getFilename()));
			
			if ($this->hasHeight()) {
				$object->setHeight($this->getHeight());
				$embed->setHeight($this->getHeight());
			} else {
				$object->setHeight(260);
				$embed->setHeight(260);
			}
					
			if ($this->hasWidth()) {
				$object->setWidth($this->getWidth());
				$embed->setWidth($this->getWidth());
			} else {
				$object->setWidth(320);
				$embed->setWidth(320);
			}
			
			$embed->setAutoplay(false);
			$embed->setController(true);
			$embed->setPluginSpace('http://www.apple.com/quicktime/download');
			
			foreach ($this->children as $child) {
				$child->doStatesUntil('preRender');
			}			
		}		
		
		parent::render();
	}
}

/*class THtmlEmbedWMV extends TCompositeControl {

	private $filename;
	
	public function setFilename($fn) {
		$this->filename = $fn;
	}
	
	public function getFilename() {
		return $this->filename;
	}
	
	public function hasFilename() {
		return !empty($this->filename);
	}
	
	private $height;
	
	public function setHeight($h) {
		$this->height = $h;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function hasHeight() {
		return !empty($this->height);
	}
	
	private $width;
	
	public function setWidth($w) {
		$this->width = $w;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function hasWidth() {
		return !empty($this->width);
	}
	
	public function render() {
	
		if ($this->hasFilename()) {
		
			$this->children->deleteAll();
			
			$object = $this->children[] = zing::create('THtmlObject',
							array(	'classid' => 'CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95',
									'standby' => 'Loading Video...',
									'type' => 'application/x-oleobject'));
			$object->children[] = zing::create('THtmlParam', array('name' => 'FileName', 'value' => $this->getFilename()));
			$object->children[] = zing::create('THtmlParam', array('name' => 'autostart', 'value' => false));
			$object->children[] = zing::create('THtmlParam', array('name' => 'ShowControls', 'value' => true));
			$object->children[] = zing::create('THtmlParam', array('name' => 'ShowStatusBar', 'value' => false));
			$object->children[] = zing::create('THtmlParam', array('name' => 'ShowDisplay', 'value' => false));
			$embed = $object->children[] = zing::create('THtmlEmbed', array('src' => $this->getFilename()));
			
			if ($this->hasHeight()) {
				$object->setHeight($this->getHeight());
				$embed->setHeight($this->getHeight());
			} else {
				$object->setHeight(260);
				$embed->setHeight(260);
			}
					
			if ($this->hasWidth()) {
				$object->setWidth($this->getWidth());
				$embed->setWidth($this->getWidth());
			} else {
				$object->setWidth(320);
				$embed->setWidth(320);
			}
				
			$embed->setName('MediaPlayer');
			$embed->setType('application/x-mplayer2');
			$embed->setShowControls(true);
			$embed->setShowStatusBar(false);
			$embed->setShowDisplay(false);
			$embed->setAutostart(false);
			
			foreach ($this->children as $child) {
				$child->doStatesUntil('preRender');
			}			
		}		
		
		parent::render();
	}
}
*/

class THtmlObject extends THtmlControl {
	
	public function __construct($params = array()) {
		parent::__construct();
		$this->setTag('object');
		$this->parseParams($params);
	}		
	
	public function setWidth($w) {
		$this->attributes['width'] = $w;
	}
	
	public function getWidth() {
		return $this->attributes['width'];
	}
	
	public function setHeight($h) {
		$this->attributes['height'] = $h;
	}
	
	public function getHeight() {
		return $this->attributes['height'];
	}
	
	public function setClassId($classid) {
		$this->attributes['classid'] = $classid;
	}
	
	public function getClassID() {
		return $this->attributes['classid'];
	}
	
	public function setStandBy($s) {
		$this->attributes['standby'] = $s;
	}
	
	public function getStandBy() {
		return $this->attributes['standby'];
	}
	
	public function setType($type) {
		$this->attributes['type'] = $type;
	}
	
	public function getType() {
		return $this->attributes['type'];
	}
	
	public function setCodebase($cb) {
		$this->attributes['codebase'] = $cb;
	}
	
	public function getCodebase() {
		return $this->attributes['codebase'];
	}
}
	
class THtmlParam extends THtmlControl {
	
	public function __construct($params = array()) {
		parent::__construct();
		$this->setTag('param');
		$this->parseParams($params);
	}		
	
	public function setName($name) {
		$this->attributes['name'] = $name;
	}
	
	public function getName() {
		return $this->attributes['name'];
	}
	
	public function setValue($value) {
		$this->attributes['value'] = $value;
	}
	
	public function getValue() {
		return $this->attributes['value'];
	}
	
}

class THtmlEmbed extends THtmlControl {

	public function __construct($params = array()) {
		parent::__construct();
		$this->setTag('embed');
		$this->parseParams($params);
	}		
	
	public function setType($type) {
		$this->attributes['type'] = $type;
	}
	
	public function getType() {
		return $this->attributes['type'];
	}
	
	public function setSrc($src) {
		$this->attributes['src'] = $src;
	}
	
	public function getSrc() {
		return $this->attributes['src'];
	}
	
	public function setName($name) {
		$this->attributes['name'] = $name;
	}
	
	public function getName() {
		return $this->attributes['name'];
	}
	
	public function setWidth($width) {
		$this->attributes['width'] = $width;
	}
	
	public function getWidth() {
		return $this->attributes['width'];
	}
	
	public function setHeight($height) {
		$this->attributes['height'] = $height;
	}
	
	public function getHeight() {
		return $this->attributes['height'];
	}
	
	public function setShowControls($show) {
		$this->attributes['showControls'] = zing::evaluateAsBoolean($show) ? 1 : 0;
	}
	
	public function getShowControls() {
		return $this->attributes['showControls'];
	}
	
	public function setShowStatusBar($show) {
		$this->attributes['showStatusBar'] = zing::evaluateAsBoolean($show) ? 1 : 0;
	}
	
	public function getShowStatusBar() {
		return $this->attributes['showStatusBar'];
	}
	
	public function setShowDisplay($show) {
		$this->attributes['showDisplay'] = zing::evaluateAsBoolean($show) ? 1 : 0;
	}
	
	public function getShowDisplay() {
		return $this->attributes['showDisplay'];
	}
	
	public function setAutoStart($auto) {
		$this->attributes['autoStart'] = zing::evaluateAsBoolean($auto) ? 1 : 0;
	}
	
	public function getAutoStart() {
		return $this->attributes['autoStart'];
	}
	
	public function setAutoplay($auto) {
		$this->attributes['autoPlay'] = zing::evaluateAsBoolean($auto) ? 'true' : 'false';
	}
	
	public function getAutoPlay() {
		return zing::evaluateAsBoolean($this->attributes['autoPlay']);
	}
	
	public function setController($controller) {
		$this->attributes['controller'] = zing::evaluateAsBoolean($controller) ? 'true' : 'false';
	}
	
	public function getController() {
		return zing::evaluateAsBoolean($this->attributes['controller']);
	}
	
	public function setPluginSpace($p) {
		$this->attributes['pluginSpace'] = $p;
	}
	
	public function getPluginSpace() {
		return $this->attributes['pluginSpace'];
	}
	
	
		
		
}

?>