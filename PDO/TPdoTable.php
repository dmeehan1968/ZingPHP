<?php

class TPdoTable {

	public $class;
	public $pdo;
	public $sort;
	public $table;
	public $columns = array();
	public $relations = array();
	public $indexes = array();
	
	const PDO_STATEMENT = '/@sql\s+(\w+)\s+([^\n]*)$/mi';
		
	public function __construct($class, ZingPDO $pdo) {
		$this->class = $class;
		$this->pdo = $pdo;
		$this->table = strtolower($class) . 's';
		
		$rc = new ReflectionClass($class);
		
		preg_match_all(self::PDO_STATEMENT,$rc->getDocComment(), $matches, PREG_SET_ORDER);

		foreach ($matches as $match) {
			switch (strtolower($match[1])) {
			case 'table':
				$this->table = trim($match[2]);
				break;
			case 'sort':
				$this->sort = trim($match[2]);
				break;
			case 'index':
				$this->indexes[] = trim($match[2]);
				break;
			default:
				throw new Exception('Unknown @sql instruction \''.$match[1].'\'');
			}
		}
		
		foreach ($rc->getProperties() as $property) {
			preg_match_all(self::PDO_STATEMENT, $property->getDocComment(), $matches, PREG_SET_ORDER);
			$name = strtolower($property->getName());
			foreach ($matches as $match) {
				switch(strtolower($match[1])) {
				case 'column':
					$column = new TPdoColumn($name, $match[2]);
					$this->columns[$name] = $column;
					break;
				case 'relation':
					if (isset($this->relations[$name])) {
						$this->relations[$name]->interpret($match[2]);
					} else {
						$this->relations[$name] = new TPdoRelation($name, $match[2]);
					}
					break;
				default:
					throw new Exception('Unknown @sql instruction \''.$match[1].'\'');
				}
			}
		}
		
		/*
		 * If no indexes defined, make the first column the primary index
		 */
		if (count($this->indexes) < 1) {
			$this->indexes[] = current($this->columns)->name;
		}
	}
	
	public function createObject() {
		$class = $this->class;
		return new $class;
	}
	
	public function isRelation($name) {
		return isset($this->relations[strtolower($name)]);
	}
		
	public function getRelated($name, $object) {
		$relation = $this->relations[strtolower($name)];
		$relatedTable = new TPdoTable($relation->getClass(), $this->pdo);
		
		$objects = new TPdoCollection($relatedTable);
		$params = array('object' => $object, $relation->getClass() => $relatedTable->table);
		$objects->query( $relation->getSelect($params) );

		if ($relation->hasMany()) {
			return $objects;
		}
		
		return $objects[0];
	}	

	public function query($sql) {
		$stmt = $this->pdo->query($sql);

		if ($stmt === false) {
			throw new Exception(implode(', ',$this->pdo->errorInfo()));
		}
		
		$stmt->setFetchMode(ZingPDO::FETCH_ASSOC);
		return $stmt;
	}		
	
	public function insert($source) {
	
		$sql = 'INSERT INTO ' . $this->table . ' (';
		$cnt = 0;
		foreach ($this->columns as $column) {
			$sql .= ($cnt++ ? ', ' : '') . $column->name;
		}
		$sql .= ') VALUES (';
		$cnt = 0;
		foreach ($this->columns as $column) {
			$property = $column->name;
			$sql .= ($cnt++ ? ', ' : '') . $this->quote($column->name, $source->$property);
		}
		$sql .= ');';

		if ($this->pdo->exec($sql) === false) {
			throw new Exception(implode(', ',$this->pdo->errorInfo()));
		}
	}
	
	public function select($constraint) {
		$sql = 'SELECT * FROM ' . $this->table;

		if (isset($constraint)) {
			$sql .= ' WHERE ' . $constraint;
		}
		
		if (isset($this->sort)) {
			$sql .= ' ORDER BY ' . $this->sort;
		}
		
		return $this->query($sql);
	}
	
	public function update($source) {
	
		$sql = 'UPDATE ' . $this->table . ' SET ';
		$cnt = 0;
		foreach ($this->columns as $column) {
			$property = $column->name;
			$sql .= ($cnt++ ? ', ' : '') . $column->name . '=' . $this->quote($property, $source->$property);
		}

		$sql .= ' WHERE ';
		
		$cnt = 0;
		foreach ($this->indexes as $index) {
			$sql .= ($cnt++ ? ' and ' : '') . $index . '=' . $this->quote($index, $source->$index);
		}

		if ($this->pdo->exec($sql) === false) {
			throw new Exception(implode(', ',$this->pdo->errorInfo()));
		}
	}
	
	public function assign($array, $object) {
	
		foreach ($this->columns as $column) {
			$property = $column->name;
			$object->$property = $array[$property];
		}
	}
	
	public function getConstraintByExample($object, $clause = 'and') {
		$constraint = '';
		$count = 0;
		foreach ($this->columns as $column) {
			$property = $column->name;
			if (! is_null($object->$property)) {
				$constraint .=	($count++ > 0 ? ' ' . $clause . ' ' : '') . $column->name . ' = ' . $this->quote($column->name, $object->$property);
			}
		}

		return $constraint;
	}
	
	public function quote($column, $value) {
		if (is_null($value)) {
			return 'null';
		}
		
		if ($this->columns[$column]->type != 'int') {
			return $this->pdo->quote($value);
		}
		
		return $value;
	}
	

}


?>