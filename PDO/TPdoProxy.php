<?php

class TPdoProxy {

	private $table;
	private $object;
	private $dirty = false;
		
	public function __construct(TPdoTable $table) {
		$this->table = $table;
		$this->object = $table->createObject();
	}
	
	public function __get($name) {
		if ($this->table->isRelation($name) && ! isset($this->object->$name)) {
			$this->object->$name = $this->table->getRelated($name, $this);
		}
	
		if(property_exists($this->object, $name)) {
			return $this->object->$name;
		}
		
		$method = 'get' . $name;
		if (method_exists($this->object, $method)) {
			return $this->$method();
		}
		
		return null;
	}
	
	public function __set($name, $value) {
		if(property_exists($this->object, $name)) {
			$this->object->$name = $value;
			$this->dirty = true;
		}
		
		$method = 'set' . $name;
		if (method_exists($this->object, $method)) {
			return $this->$method($value);
		}
	}
	
	public function __call($method, $params) {
		if (method_exists($this->object, $method)) {
			return call_user_func_array(array($this->object, $method), $params);
		}
		
		throw new Exception('Call to undefined method '.get_class($this->object).'::'.$method.'()');
	}
	
	public function getClass() {
		return get_class($this->object);
	}
	
	public function insert() {
		$this->table->insert($this->object);
	}
	
	public function setDirty($state = true) {
		$this->dirty = $state;
	}
	
	public function getDirty() {
		return $this->dirty;
	}
	
	public function find() {
		$rows = $this->table->select($this->table->getConstraintByExample($this));

		if ($row = $rows->fetch()) {
			$this->table->assign($row, $this);
			$this->setDirty(false);
			return true;
		}
		
		return false;
	}
	
	public function update() {
		if ($this->getDirty()) {
			$this->table->update($this->object);
		}
	}
		
}

?>