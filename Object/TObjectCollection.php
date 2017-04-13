<?php

class TObjectCollection implements IteratorAggregate, Countable, ArrayAccess {

	private $pdo;
	private $objects = array();
	private $object_class = 'StdClass';
	private $statement;
	private $refresh = false;
		
	public function __construct(ZingPDO $pdo, $statement, $class) {

		if ($statement === false) {
			throw new Exception(implode(', ',$pdo->errorInfo()));
		}
		
		$this->pdo = $pdo;
		$this->object_class = $class;
		$this->statement = $statement;
		
		$statement->setFetchMode(ZingPDO::FETCH_ASSOC);

		$this->loadObjects();
	}

	public function loadObjects() {
		while ($row = $this->statement->fetch()) {
			$object = $this->create($row);
			$object->setStored(true);
		}
	}
	
	public function refresh($force = false) {
		if ($this->refresh || $force) {
			if (! $this->statement->execute()) {
				throw new TObjectPdoException($this->statement);
			}
			$this->loadObjects();
			$this->refresh = false;
		}
	}
		
	public function getIterator() {
		$this->refresh();
		return new ArrayIterator($this->objects);
	}

	public function count() {
		$this->refresh();
		return count($this->objects);
	}
	
	public function OffsetGet($offset) {
		$this->refresh();
		return $this->objects[$offset];
	}
	
	public function OffsetSet($offset, $value) {
		$this->refresh();
		$this->objects[$offset] = $value;
	}
	
	public function OffsetExists($offset) {
		$this->refresh();
		return isset($this->objects[$offset]);
	}
	
	public function OffsetUnset($offset) {
		$this->refresh();
		unset($this->objects[$offset]);
	}

	public function create($values = array()) {
		$this->refresh();
		$class = $this->object_class;
		$object = $this->objects[] = new $class($this->pdo, $values);
		return $object;
	}	
	public function destroy($cascade = false) {
		$this->refresh();
		foreach ($this->objects as $object) {
			$object->destroy($cascade);
		}
		$this->objects = array();
		$this->refresh = true;
	}

	public function update() {
		foreach ($this->objects as $object) {
			$object->update();
		}
	}
}



?>