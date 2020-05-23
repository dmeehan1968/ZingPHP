<?php

class THtmlHeadComponent extends THtmlDiv {

	public function __construct($params = array()) {
		
		$this->setHideWhenEmpty(false);
		parent::__construct($params);
	}
	
	private $renderContent = false;
	
	public function setRenderContent($dc) {
		$this->renderContent = zing::evaluateAsBoolean($dc);
	}
	
	public function getRenderContent() {
		return $this->renderContent;
	}

	private $overwrite = false;
	
	public function setOverwrite($value) {
		$this->overwrite = zing::evaluateAsBoolean($value);
	}
	
	public function getOverwrite() {
		return $this->overwrite;
	}

	public function setInnerText($text, $forceEmpty = false) {
		$this->children->deleteAll();
		$this->children[] = zing::create('TPlainText', array('value' => $text));
	}
	
	public function getInnerText() {
		foreach ($this->children as $child) {
			if ($child instanceof TPlainText) {
				return $child->getValue();
			}
		}
		
		return null;
	}
	
	public function render() {
		if ($this->getRenderContent()) {
			echo $this->internalRender();
		}
	}	

	private $contentCache;
	
	public function internalRender() {
		if (! isset($this->contentCache)) {
			ob_start();
	
			parent::render();
			
			$this->contentCache = ob_get_contents();
			ob_end_clean();
		}
		return $this->contentCache;
	}
	
	public function updatePlaceholder($ph) {

		$content = $this->internalRender();
		
		if (trim($content) == '') {
			return;
		}
		
		if ($this->getOverwrite()) {
			$ph->setContent($content);
		} else {
			$ph->setContent($ph->getContent() . "\r\n" . $content);
		}
	}

}

?>