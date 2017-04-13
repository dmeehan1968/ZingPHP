<?php


class TRegistry implements IteratorAggregate, Countable, ArrayAccess, IObservable {

	private $vars = array();
	private $valid = false;

	public $observers = array();
	
	const EVT_ADD = 'ADD';
	const EVT_UPDATE = 'UPDATE';
	const EVT_DELETE = 'DELETE';
	const EVT_ITERATE = 'ITERATE';
	
	function __construct($array = array(), $stripSlashes = false) {
		foreach ($array as $name => $value) {
			$this->__set(strtolower($name), $stripSlashes && is_string($value) ? stripslashes($value) : $value);
		}
	}
	
	function notifyObservers($event, $params = null) {
		foreach ($this->observers as $ob) {
			$ob->observedEvent($this, $event, $params);
		}
	}

	public function getIterator() {
		return new ArrayObject($this->vars);
	}

	public function __get($name) {
		$name = strtolower($name);
		return $this->vars[$name];
	}
	
	public function __set($name, $value) {
		if (is_null($name)) {
			$max = -1;
			foreach ($this->vars as $index => $notused) {
				if (is_int($index)) {
					$max = max($max, $index);
				}
			}
			$name = ++$max;
		}
		
		$evt = null;
		$before = null;

		if (! $this->__isset($name)) {
			$evt = self::EVT_ADD;
		} else if (($before = $this->__get($name)) != $value) {
			$evt = self::EVT_UPDATE;
		}

		$name = strtolower($name);
				
		if ($evt) {
			$this->notifyObservers($evt, array('name' => &$name, 'before' => &$before, 'after' => &$value));
		}

		if (is_null($value)) {
			if (!is_null($name)) {
				unset($this->vars[$name]);
			}
		} else {
			if (is_null($name)) {
				$this->vars[] = $value;	// force append
			} else {
				$this->vars[strtolower($name)] = $value;
			}
		}
	}

	public function __isset($name) {
		return isset($this->vars[strtolower($name)]);
	}
	
	public function __unset($name) {
		$name = strtolower($name);
		$this->notifyObservers(self::EVT_DELETE, array('name' => $name));
		unset($this->vars[$name]);
	}
	
	public function insertBefore($key, $value, $name = null) {
		foreach (array_keys($this->vars) as $offset => $keyname) {
			if ($key == $keyname) {
				break;
			}
		}
		
		if ($key != $keyname) {
			return false;	// non-existent insertion point
		}

		$before = array_slice($this->vars, 0, $offset, true);
		$after = array_slice($this->vars, $offset, count($this->vars), true);

		$this->vars = $before;
		$this->__set($name, $value);
		$new = array_slice($this->vars, -1);
		$this->vars = array_merge($this->vars, $after);
		return $new[0];
	}
	
	public function offsetExists($offset) {
		return $this->__isset($offset);
	}
	
	public function offsetGet($offset) {
		return $this->__get($offset);
	}
	
	public function offsetSet($offset, $value) {
		return $this->__set($offset, $value);
	}
	
	public function offsetUnset($offset) {
		return $this->__unset($offset);
	}

	public function count() {
		return count($this->vars);
	}
	
	public function asArray() {
		return $this->vars;
	}
	
	public function deleteAll() {
		$this->vars = array();
	}
}


?>