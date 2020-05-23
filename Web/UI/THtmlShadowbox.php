<?php

class THtmlShadowBox extends TCompositeControl {

	private $loader;

	public function __construct($params = array()) {

		$css = zing::create('THtmlStyle', array('href' => "/Zing/Assets/Scripts/shadowbox-build-3.0rc1/shadowbox.css", 'type' => 'text/css', 'rel' => 'stylesheet'));
		$script = zing::create('THtmlScript', array('src' => "/Zing/Assets/Scripts/shadowbox-build-3.0rc1/shadowbox.js"));
		$init = zing::create('THtmlScript', array('innerText' => 'Shadowbox.init({ language: \'en\', players: [\'img\', \'html\', \'iframe\', \'qt\', \'wmp\', \'swf\', \'flv\']});'));

		parent::__construct($params);

		$this->children[] = $css;
		$this->children[] = $script;
		$this->children[] = $init;

	}

}

?>
