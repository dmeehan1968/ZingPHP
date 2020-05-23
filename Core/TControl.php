<?php

class TControl implements IBindable, IContained, IControlState, IEvent, IIdentity, IObservable, IVisibility {

	public $session;

	function __construct($params = array()) {

		$this->authManager = TAuthentication::getInstance();
		$this->session = TSession::getInstance();

		$this->parseParams($params);
	}

	public function parseParams($params) {
		if (!is_array($params)) {
			$params = array('id' => $params);
		}

		if (!isset($params['id'])) {
			$params['id'] = null;
		}
		foreach ($params as $property => $value) {
			$method = 'set' . $property;
			$this->$method($value);
		}
	}

	/* =========================== IOBSERVABLE =============================*/

	public $observers = array();

	public function notifyObservers($event, $params = array()) {
		foreach ($this->observers as $ob) {
			$ob->observedEvent($this, $event, $params);
		}
	}

	/* ============================ IIDENTITY ==============================*/

	private $id;

	public function getID() {
		return $this->id;
	}

	public function setID($id) {
		$this->id = $id;
	}

	public function hasID() {
		return isset($this->id);
	}

	/* =========================== ICONTAINED ==============================*/

	private $container;

	public function getContainer($class = NULL) {
		if (! is_null($class)) {
			if ($this->container instanceof $class) {
				return $this->container;
			} else {
				return $this->container ? $this->container->getContainer() : NULL;
			}
		}
		return $this->container;
	}

	public function setContainer($container) {
		$this->container = $container;
	}

	public function hasContainer() {
		return isset($this->container);
	}

	public function getTopControl() {
		$control = $this;
		while ($control instanceof IContained && $control = $control->getContainer()) {
			// just loop
		}
		return $control;
	}

	/* ========================= ICONTROLSTATE ============================*/

	public function preInit() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->preInit()');
		$this->notifyObservers('preInit');
		$this->fireEvent('onPreInit', $this);
	}
	public function init() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->init()');
		$this->notifyObservers('init');
		$this->fireEvent('onInit', $this);
	}
	public function initComplete() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->initComplete()');
		$this->notifyObservers('initComplete');
		$this->fireEvent('onInitComplete', $this);
	}
	public function auth() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->auth()');
		$this->notifyObservers('auth');
		$this->fireEvent('onAuth', $this);
	}
	public function preLoad() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->preLoad()');
		$this->notifyObservers('preLoad');
		$this->fireEvent('onPreLoad', $this);
	}
	public function load() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->load()');
		$this->notifyObservers('load');
		$this->fireEvent('onLoad', $this);
		if ($this->hasOnLoad()) {
			$this->fireEvent($this->getOnLoad(), $this);
		}
	}
	public function loadComplete() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->loadComplete()');
		$this->notifyObservers('loadComplete');
		$this->fireEvent('onLoadComplete', $this);
	}
	public function prePost() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->prePost()');
		$this->notifyObservers('prePost');
		$this->fireEvent('onPrePost', $this);
	}
	public function post() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->post()');
		$this->notifyObservers('post');
		$this->fireEvent('onPost', $this);
	}
	public function postComplete() {
		$this->notifyObservers('postComplete');
		$this->fireEvent('onPostComplete', $this);
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->postComplete()');
	}
	public function preRender() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->preRender()');

		$this->bind();

		$this->notifyObservers('preRender');
		$this->fireEvent('onPreRender', $this);
		if ($this->hasOnPreRender()) {
			$this->fireEvent($this->getOnPreRender(), $this);
		}
	}

	public function render() {
		if ($this->getVisible() && $this->hasPermission()) {
	//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->render()');
			$this->notifyObservers('render');
			$this->fireEvent('onRender', $this);
			if ($this->hasOnRender()) {
				$this->fireEvent($this->getOnRender(), $this);
			}
		}
	}
	public function renderComplete() {
//		zing::debug(get_class($this) . '(' . $this->getid() . ')' . '->renderComplete()');
		$this->notifyObservers('renderComplete');
		$this->fireEvent('onRenderComplete', $this);
	}

	public function doStatesUntil($state) {

		foreach ($this->states as $current) {
			$this->$current();
			if (strtolower($state) == strtolower($current)) {
				return;
			}
		}
		return;
	}

	private $states = array('preInit', 'init', 'initComplete', 'auth', 'preLoad', 'load', 'loadComplete', 'prePost', 'post', 'postComplete', 'preRender', 'render', 'renderComplete');

	public function addState($newState, $beforeState = null) {
		$prefix = $this->states;
		$suffix = array();

		if ($beforeState) {
			$pos = array_search($beforeState, $this->states);
			if ($pos !== false) {
				$prefix = array_slice($this->states, 0, $pos);
				$suffix = array_slice($this->states, $pos);
			}
		}

		$this->states = array_merge($prefix, array($newState), $suffix);
	}

	/* ============================= IEVENT ===============================*/


	/**
	 * fireEvent method
	 *
	 * Call a matching method ('action') in the current class if it is defined,
	 * or pass the event up the container order to find any match
	 *
	 * Event handlers receive two parameters, 1) the control that fired the
	 * event, and 2) the parameter array.  Event handlers should return a boolean
	 * to indicate if event bubbling should continue, true to continue event
	 * bubbling, false to terminate.
	 *
	 * @param string $action
	 *		the name of the method to call
	 * @param TControl $control
	 *		the TControl object that fired the event
	 * @param array $params
	 * 	an array of parameters for the event.  Receiving methods	can modify
	 *		the parameter to affect the outcome of the control on return from the
	 *		event
	 *
	 * @return boolean
	 *		false if the event was handled by a method, or true if no event
	 *		handler was found.
	 */
	public function fireEvent($action, $control, $params = array()) {

		if (method_exists($this, $action)) {
			if (! $this->$action($control, $params)) {
				return false;
			}
		}

		if ($this instanceof IContained && $this->hasContainer() && $this->getContainer() instanceof IEvent) {
			return $this->getContainer()->fireEvent($action, $control, $params);
		}

//		throw new Exception('unhandled action "' . $action . '" from ' . get_class($control) . '::' . $control->getId());

		return true;
	}

	/* ============================= IBINDABLE ============================*/

	private	$boundObject;
	private	$boundProperty;

	public function setBoundObject($object) {
		if (is_null($object)) {
			unset($this->boundObject);
		} else {
			$this->boundObject = $object;
		}
	}

	public function getBoundObject() {
		if (isset($this->boundObject)) {
			return $this->boundObject;
		}

		if ($this instanceof IContained && $this->hasContainer() && $this->getContainer() instanceof IBindable) {
			return $this->getContainer()->getBoundObject();
		}

		return null;
	}

	public function hasBoundObject() {
		return ! is_null($this->getBoundObject());
	}

	public function hasOwnBoundObject() {
		return ! is_null($this->boundObject);
	}

	public function bind() {

		// implementation required in inherited classes
	}

	public function setBoundProperty($property) {
		if (!empty($property)) {
			$this->boundProperty = $property;
		} else {
			unset($this->boundProperty);
		}
	}

	public function getBoundProperty() {
		return $this->boundProperty;
	}

	public function hasBoundProperty() {
		return isset($this->boundProperty);
	}

	public static function resolveBoundValue($object, $property) {

		$propertySet = explode('|', $property);
		$baseObject = $object;

		foreach ($propertySet as $property) {

			$object = $baseObject;

			preg_match_all('/->(?P<object>\w+)|\[(?P<array>\w+)\]|(?P<property>\w+)/i', $property, $matches, PREG_SET_ORDER);

			foreach ($matches as $match) {
				if (!empty($match['object'])) {

					$member = $match['object'];
					$object = $object->$member;

				} else if (!empty($match['array'])) {

					$member = $match['array'];
					$object = $object[$member];

				} else if (!empty($match['property'])) {

					$member = $match['property'];
					if (is_array($object)) {
						$object = $object[$member];
					} else {
						$object = $object->$member;
					}
				}
			}

			if (! is_null($object)) {
				break;
			}

		}

		return $object;
	}

	/* ============================ AUTHENTICATION ==========================*/

	public	$authManager;
	private	$authGroups = array();
	private	$authRoles = array();
	private	$authPerms = array();
	private	$authGuest;

	private function setAuthItems($type, $items) {
		if (is_string($items)) {
			$items = explode(' ', $items);
		}
		$this->$type = array_merge($this->$type, (array) $items);
	}

	public function setAuthGroups($groups) {
		$this->setAuthItems('authGroups', $groups);
	}

	public function getAuthGroups() {
		return $this->authGroups;
	}

	public function setAuthRoles($roles) {
		$this->setAuthItems('authRoles', $roles);
	}

	public function getAuthRoles() {
		return $this->authRoles;
	}

	public function setAuthPerms($perms) {
		$this->setAuthItems('authPerms', $perms);
	}

	public function getAuthPerms() {
		return $this->authPerms;
	}

	public function setAuthGuest($bool) {
		if (!is_null($bool)) {
			$this->authGuest = zing::evaluateAsBoolean($bool);
		}
	}

	public function getAuthGuest() {
		return $this->authGuest;
	}

	public function hasPermission() {
		return $this->authManager->checkCredentials($this->authGroups, $this->authRoles, $this->authPerms, $this->authGuest);
	}

	public function cloneAuth($srcControl) {
		$this->setAuthGroups($srcControl->getAuthGroups());
		$this->setAuthRoles($srcControl->getAuthRoles());
		$this->setAuthPerms($srcControl->getAuthPerms());
		$this->setAuthGuest($srcControl->getAuthGuest());
	}

	/* ============================= IVISIBILITY ============================*/

	private $visible = true;

	public function setVisible($value) {
		$this->visible = $value;
	}

	public function getVisible() {
		return $this->visible;
	}

	public function isVisible() {
		return $this->getVisible();
	}

	/* ============================= DEFAULT EVENTS ============================*/

	private $onLoadEvent;

	public function setOnLoad($action) {
		$this->onLoadEvent = $action;
	}

	public function getOnLoad() {
		return $this->onLoadEvent;
	}

	public function hasOnLoad() {
		return isset($this->onLoadEvent);
	}

	private $onPreRenderEvent;

	public function setOnPreRender($action) {
		$this->onPreRenderEvent = $action;
	}

	public function getOnPreRender() {
		return $this->onPreRenderEvent;
	}

	public function hasOnPreRender() {
		return isset($this->onPreRenderEvent);
	}
	private $onRenderEvent;

	public function setOnRender($action) {
		$this->onRenderEvent = $action;
	}

	public function getOnRender() {
		return $this->onRenderEvent;
	}

	public function hasOnRender() {
		return isset($this->onRenderEvent);
	}

}

?>
