<?php

/**
 * IBindable Interface
 *
 * Defines methods to allow an object to have a bound datasource
 *
 * @package Core
 * @since 0.1
 */
interface IBindable {
	public function setBoundObject($object);
	public function getBoundObject();
	public function hasBoundObject();
	public function setBoundProperty($property);
	public function getBoundProperty();
	public function hasBoundProperty();
	public function bind();
}

/**
 * IContained Interface
 *
 * Defines methods for classes that are contained by other objects
 *
 * @package Core
 * @since 0.1
 */
interface IContained {
	public function setContainer($container);
	public function getContainer();
	public function hasContainer();
	public function getTopControl();
}

/**
 * IContainer interface
 *
 * Defines methods for classes that contain other objects
 *
 * @package Core
 * @since 0.1
 */
interface IContainer {
	/**
	 * Get all of the children
	 */
	public function getChildren();
	/**
	 * Get a descendant by id
	 */
	public function getDescendantById($id);
	/**
	 * Get a descendant by class
	 */
	public function getDescendantsByClass($class);
}

/**
 * IControlState Interface
 *
 * Define methods for classes that implement control state
 *
 * @package Core
 * @since 0.1
 */
interface IControlState {
	public function preInit();
	public function init();
	public function initComplete();
	public function preLoad();
	public function load();
	public function loadComplete();
	public function prePost();
	public function post();
	public function postComplete();
	public function preRender();
	public function render();
	public function renderComplete();
	public function doStatesUntil($state);
}

/**
 * IEvent Interface
 *
 * Defines methods to allow a class to fire an event
 */
interface IEvent {
	public function fireEvent($action, $control, $params = array());
}

/**
 * IIdentity Interface
 *
 * Defines methods for objects that support identity
 *
 * @package Core
 * @since 0.1
 */
interface IIdentity {
	public function setID($id);
	public function getID();
	public function hasID();
}



/**
 * IObservable Interface
 *
 * Defines methods for classes that can be observed by other objects.
 *
 * @package Core
 * @since 0.1
 */
interface IObservable {
	public function notifyObservers($event, $params = array());
}

/**
 * IObserver Interface
 *
 * defines methods for classes that observe other objects
 *
 * @package Core
 * @since 0.1
 */
interface IObserver {
	public function observedEvent($object, $event, $params = array());
}

/**
 * ISingleton Interface
 *
 * Defines methods for classes that implement a single instance
 *
 * @package Core
 * @since 0.1
 */
interface ISingleton {
	public static function getInstance();
}

/**
 * IVisibility interface
 *
 * Defines methods for classes that implement visibility
 */
interface IVisibility {
	public function setVisible($value);
	public function getVisible();
}

?>
