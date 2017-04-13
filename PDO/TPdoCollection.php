<?php

class TPdoCollection extends ArrayObject {

	private $table;
	
	public function __construct(TPdoTable $table) {
		parent::__construct();
		$this->table = $table;
	}
	
	public function find($constraint = null) {
		$rows = $this->table->select($constraint);
		$this->populate($rows);
	}
	
	public function query($sql) {
		$rows = $this->table->query($sql);
		$this->populate($rows);
	}
	
	public function populate($rows) {
		while ($row = $rows->fetch()) {
			$c = zingPdo::createObject($this->table->class, $this->table->pdo);
			$this->table->assign($row, $c);
			$c->setDirty(false);
			$this->append($c);
		}
	}
}


?>