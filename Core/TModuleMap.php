<?php

/**
 * TModuleMapBase
 *
 * Abstract class to define common methods for module and layout maps
 */
abstract class TModuleMapBase {

	/**
	 * The base path to the module (excluding extension)
	 */
	private $modulePath;
	
	/**
	 * The path to the php script
	 */
	private $moduleScript;
	
	/**
	 * The path to the module template
	 */
	private $moduleTemplate;
	
	/**
	 * The class defined by the module
	 */
	private $moduleClass;
		
	/**
	 * @param $modulePath
	 *				the base path to the module (exluding extension)
	 */
	public function __construct($modulePath) {
		if (substr($modulePath,0,1) != '/') {
			$sess = TSession::getInstance();
			$modulePath = $sess->paths->base . $modulePath;
		}
		
		$this->modulePath = $modulePath;
		$this->moduleScript = $modulePath . '.php';
		$this->moduleTemplate = $modulePath . '.tpl';
		$this->moduleClass = array_pop(explode('/',$modulePath));
		
		if (!isset(zing::$aliases[$this->moduleClass])) {
			zing::$aliases[$this->moduleClass] = $this->moduleScript;
		}
	}
	
	/**
	 * return the module path
	 */
	public function getModulePath() {
		return $this->modulePath;
	}
	
	/**
	 * return an new instance of the module
	 */
	public function getModule() {
		$class = $this->moduleClass;
		$module = new $class;
		if ($module instanceof TTemplateControl) {
			$module->setTemplatePath($this->moduleTemplate);
		}
		return $module;
	}
				
}

/**
 * TModuleMap class
 *
 * Provides a map between a module and the URI used to access it
 *
 * e.g. "http://domain/path" maps to "/customer/customerlist"
 */

class TModuleMap extends TModuleMapBase {

	/**
	 * The regular expression used to match the uri to this module
	 */
	private $regexp;
	
	/**
	 * The URI template used to access this module
	 *
	 * Parameters can be substituted by enclosing the parameter index
	 * in {} characters. e.g.
	 *
	 * /customer/{id}
	 *
	 * where params = array('id' => 1) will generate
	 * 
	 * /customer/1
	 *
	 */
	private $uri;
	
	/**
	 * Parameters derived from the match with the URI
	 */
	public  $parameters = array();

	/**
	 * Parameters used to generate matching uri's
	 */
	public $factory;
			
	/**
	 * @param $modulePath
	 *				The base path to the module (excluding the .php extension)
	 *
	 * @param $regexp
	 *				The regular expression used to match the URI to this module
	 *
	 * @param $uri
	 *				The uri template used to match this module
	 *
	 * @param $layout
	 *				The layout used to contain module output
	 *
	 * @param $params
	 *				Array for name/value params to be passed in
	 *		
	 * @param $factory
	 *				The params required to generate matching uri's
	 */
	public function __construct($modulePath, $regexp, $uri, $layout = null, $factory = null, $params = null) {

		parent::__construct($modulePath);

		$this->layout = $layout;
		$this->uri = $uri;
		$this->regexp = '/' . str_replace('/', '\\/', $regexp) . '/i';
		$this->factory = $factory;
		$this->parameters = is_array($params) ? $params : array();
	
	}

	/**
	 * getUri
	 *
	 * return a URI for this module, substituting parameter values where specified
	 *
	 * @param $params
	 *				Array of parameter values to substitute into the uri
	 */	
	public function getUri($params = array()) {
		if (preg_match_all('/(?:{(\w+)})/', $this->uri, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) > 0) {
			$uri = $this->uri;
			$adjust = 0;
			foreach ($matches as $match) {
				$uri = substr_replace($uri, $params[$match[1][0]], $match[0][1] + $adjust, strlen($match[0][0]));
				$adjust += strlen($params[$match[1][0]]) - strlen($match[0][0]);
			}
			return $uri;
			
		} else {
			return $this->uri;
		}		
	}
	
	/**
	 * isMatch
	 *
	 * Does this module map match the uri specified
	 *
	 * @param $uri
	 *				The uri to match
	 */
	public function isMatch($uri) {
		if (preg_match_all($this->regexp, urldecode($uri), $matches) > 0) {
			$this->parameters['_uri'] = $matches[0][0];
			foreach ($matches as $index => $match) {
				if (is_string($index)) {
					$this->parameters[$index] = $match[0];
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * getLayout
	 *
	 * Return the layout module for the current module
	 */
	public function getLayout() {
		return $this->layout;
	}

	public function hasFactory() {
		return isset($this->factory);
	}
	
}
?>