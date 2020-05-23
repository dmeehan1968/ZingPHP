<?php

class TSession extends TRegistry implements ISingleton {

	public $paths;
	public $parameters;

	function __construct() {
		$this->paths = new TPaths;
		$this->parameters = new TParameters;
	}

	private static $instance;

	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new TSession;
		}

		return self::$instance;
	}
}


?>
