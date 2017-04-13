<?php

/*
 * Session Parameters:
 * 		yahoo.api.base				- path to yahoo API files
 * 		yahoo.api.loader.filter		- DEBUG, RAW or empty
 * 		yahoo.api.default.skin		- default skin name, e.g. 'sam'
 * 		yahoo.api.default.skin.base	- default skin base, e.g. 'assets/skins
 *
 */

class TYuiLoader extends TCompositeControl {

	private static $instance;
	private static $first;
	
	public function __construct($params) {
		if (! self::$instance) {
			self::$instance = new _TYuiLoader;
		}
		if (! self::$first) {
			self::$first = $this;
		}
		parent::__construct($params);
	}
	
	private $require = array();
	
	public function setRequire($require) {
		if (is_string($require)) {
			$require = explode(',', $require);
		}
		foreach ($require as $value) {
			$this->require[] = trim($value);
		}
		$this->require = array_unique($this->require);
	}
	
	public function getRequire() {
		return $this->require;
	}
	
	private $loadOptional = false;
	
	public function setLoadOptional($opt) {
		$this->loadOptional = $opt;
	}
	
	public function getLoadOptional() {
		return $this->loadOptional;
	}
	
	private $onSuccess;
	
	public function setOnSuccess($success) {
		$this->onSuccess = $success;
	}
	
	public function getOnSuccess() {
		return $this->onSuccess;
	}
	
	public function hasOnSuccess() {
		return !empty($this->onSuccess);
	}
	
	private $allowRollup = true;
	
	public function setAllowRollup($rollup) {
		$this->allowRollup = $rollup;
	}
	
	public function getAllowRollup() {
		return $this->allowRollup;
	}
	
	private $base;
	
	public function setBase($base) {
		$this->base = $base;
	}
	
	public function getBase() {
		return $this->base;
	}
	
	public function hasBase() {
		return !empty($this->base);
	}

	private $modules = array();
	
	public function setAddModule($module) {
		$this->modules[] = $module;
	}
	
	public function getModules() {
		return $this->modules;
	}

	private $filter;
	
	public function setFilter($filter) {
		$this->filter = $filter;
	}
	
	public function getFilter() {
		return $this->filter;
	}
	
	public function hasFilter() {
		return !empty($this->filter);
	}
	
	public function preRender() {
		
		//
		// need to store properties locally and insert into the loader at this point, and only
		// if $this->hasPermission() and $this->getVisible()
		//

		if ($this->hasPermission() && $this->getVisible()) {
			foreach($this->require as $require) {
				self::$instance->addRequire($require);
			}
			
			self::$instance->setLoadOptional($this->getLoadOptional());
			if ($this->hasOnSuccess()) {
				self::$instance->addOnSuccess($this->getOnSuccess());
			}
			self::$instance->setAllowRollup($this->getAllowRollup());
			if ($this->hasBase()) {
				self::$instance->setBase($this->getBase());
			}
			foreach ($this->getModules() as $module) {
				self::$instance->addModule($module);
			}
			if ($this->hasFilter()) {
				self::$instance->setFilter($this->getFilter());
			}
		}		
		
		if ($this === self::$first) {
			$ctrl = $this->children[] = zing::create('THtmlScript', array('src' => self::$instance->getBase() . 'yuiloader/yuiloader-beta-min.js'));
			$ctrl->doStatesUntil('postComplete');
		}

		parent::preRender();
		
	}
	
	public function render() {
		
		if ($this === self::$first) {
			$ctrl = $this->children[] = zing::create('THtmlScriptInline', array('innerText' => self::$instance->getScript()));
			$ctrl->doStatesUntil('preRender');
		}
		parent::render();
		
	}
}


class _TYuiLoader {

	private $require = array();
	
	public function addRequire($require) {
		$this->require[$require] = true;
	}
	
	public function getRequire() {
		return $this->require;
	}

	private $loadOptional = false;
	
	public function setLoadOptional($load) {
		$this->loadOptional = $load;
	}
	
	public function getLoadOptional() {
		return $this->loadOptional;
	}
	
	private $onSuccess = array();
	
	public function addOnSuccess($function) {
		$this->onSuccess[] = $function;
	}
	
	public function getOnSuccess() {
		return $this->onSuccess;
	}

	public function hasOnSuccess() {
		return !empty($this->onSuccess);
	}
	
	private $allowRollup = true;
	
	public function setAllowRollup($allow) {
		$this->allowRollup = $allow;
	}
	
	public function getAllowRollup() {
		return $this->allowRollup;
	}
	
	private $base;
	
	public function setBase($base) {
		$this->base = $base;
	}
	
	public function getBase() {
		$base = $this->base;
		
		if (empty($base)) {
			$sess = TSession::getInstance();
			$base = $sess->parameters['yahoo.api.base'];
		}
		
		if (empty($base)) {
			$base = '/Zing/Assets/Scripts/yui/';
		}
		
		if ($base[strlen($base)-1] != '/') {
			$base .= '/';
		}

		return $base;
	}
	
	private $filter;
	
	public function setFilter($filter) {
		$this->filter = $filter;
	}
	
	public function getFilter() {
		$filter = $this->filter;
		if (empty($filter)) {
			$sess = TSession::getInstance();
			$filter = $sess->parameters['yahoo.api.loader.filter'];
		}
		return $filter;
	}
	
	public function hasFilter() {
		$filter = $this->getFilter();
		return !empty($filter);
	}
	
	private $skin;
	
	public function setSkin($skin) {
		$this->skin = $skin;
	}
	
	public function getSkin() {
		$skin = $this->skin;
		if (empty($skin)) {
			$sess = TSession::getInstance();
			$skin = $sess->parameters['yahoo.api.default.skin'];
		}
		
		if (empty($skin)) {
			$skin = 'sam';
		}
		
		return $skin;
	}
	
	public function hasSkin() {
		return !empty($this->skin);
	}
	
	private $skinBase;
	
	public function setSkinBase($base) {
		$this->skinBase = $base;
	}
	
	public function getSkinBase() {
		$base = $this->skinBase;
		if (empty($base)) {
			$sess = TSession::getInstance();
			$base = $sess->parameters['yahoo.api.default.skin.base'];
		}
		
		if (empty($base)) {
			$base = 'assets/skins/';
		}
		
		return $base;
	}

	private $modules = array();
	
	public function addModule($module) {
		// extract the name argument so we don't duplicate
		if (preg_match('/\bname\s*:\s*"([^"]*)"/',$module,$match)) {
			$name = $match[1];
			$this->modules[$name] = $module;
		}
	}
	
	public function getScript() {
	
		$script = 'var _yui_loader = new YAHOO.util.YUILoader({ require: [';
		$cnt = 0;
		foreach ($this->require as $require => $notused) {
			$script .= ($cnt++ ? ', ' : '') . '"'.$require.'"';
		}
		$script .= ']';
		
		if ($this->hasSkin()) {
			$script .= ', skin: { defaultSkin: "' . $this->getSkin() . '", base: "' . $this->getSkinBase() . '" }';
		}
		
		$script .= ', base: "' . $this->getBase() . '"';
		if ($this->hasFilter()) {
			$script .= ', filter: "' . $this->getFilter() . '"';
		}
		$script .= ', loadOptional: ' . ($this->getLoadOptional() ? 'true' : 'false');
		$script .= ', allowRollup: ' . ($this->getAllowRollup() ? 'true' : 'false');
		if ($this->hasOnSuccess()) {
			$script .= ', onSuccess: ' . $this->makeScriptFromFunctionArray($this->getOnSuccess());
		}
	
		$script .= ' });';
				
		foreach ($this->modules as $name => $module) {
			$script .= "\n//--\n" . ' _yui_loader.addModule({' . $module . '});';
		}
		
		$script .= "\n//--\n" . ' _yui_loader.insert();';
		
		return $script;
	}
	
 	private function makeScriptFromFunctionArray($array = array()) {
		$index = -1;
		$script = '';
		foreach ($array as $index => $statement) {
			if ($index == 0) {
				$script .= 'function() {';
			}
			$script .= "\n//--\n" . $statement;
		}
		
		if ($index >= 0) {
			$script .= '}';
		}
		return $script;
	}
}

?>