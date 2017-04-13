<?php

class TLayout extends TModule {

	private	$content;
	
	public function setContent($content) {
		$this->content = $content;
		$this->content->setContainer($this);
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function hasContent() {
		return isset($this->content);
	}

}

?>