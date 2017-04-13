<?php

class THtmlRandomAdvert extends THtmlDiv {

	private $src;
	
	public function setSrc($src) {
		$this->src = $src;
	}
	
	public function hasSrc() {
		return isset($this->src);
	}
	
	public function getSrc() {
		return $this->src;
	}
	
	public function preRender() {
		
		parent::preRender();
		
		$this->children->deleteAll();
		
		if ($this->hasSrc()) {

			$path = ROOTPATH . $this->getSrc();
			
			$files = scandir($path);
			
			$adverts = array();
			
			foreach ($files as $file) {
				list($filename, $ext) = explode('.', $file);
				if (in_array(strtolower($ext), array('png', 'gif', 'jpg'))) {
					$adverts[] = $this->getSrc() . DIRECTORY_SEPARATOR . $filename . '.' . $ext;
				}
			}
			
			$index = rand(0, count($adverts) - 1);
			
			$img = $this->children[] = zing::create('THtmlImage', array('src' => $adverts[$index]));
			
			$img->doStatesUntil('preRender');

		}
		
		parent::preRender();
	}
}


?>