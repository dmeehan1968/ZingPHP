<?php

abstract class TObjectPersistence {

	public $pdo;
	private $columns = array();
	private $dirty;
	private $dirtyColumns = array();
	private $stored = false;

	const	CASCADE = true;

	public function __construct($pdo, $params = array()) {
		$this->pdo = $pdo;

		foreach ($this->getColumnNames() as $col) {
			$this->columns[$col] = $this->$col;
			unset($this->$col);
		}

		$this->processParams($params);

		$this->setDirty(false);

	}

	public function processParams($params = array()) {
		foreach ($params as $param => $value) {
			if (array_key_exists($param, $this->columns)) {
				$this->columns[$param] = $value;
			}
		}
	}

	public function __get($name) {

		if (array_key_exists($name, $this->columns)) {
			return $this->columns[$name];
		}

		$loader = 'load'.$name;
		if (method_exists($this, $loader)) {
			$this->columns[$name] = $this->$loader();
			return $this->columns[$name];
		} else {
			$getter = 'get'.$name;
			if (method_exists($this, $getter)) {
				return $this->$getter();
			}
		}
	}

	public function __set($name, $value) {
		if (array_key_exists($name, $this->columns)) {
			if ($value instanceof TDateTime) {
				$value = (string) $value;
			}
			if ($this->columns[$name] !== $value) {
				$this->columns[$name] = $value;
				$this->dirtyColumns[$name] = true;
				$this->setDirty();
			}
		} else {
			$this->$name = $value;
		}
	}

	public function setDirty($value = true) {
		$this->dirty = $value;
		if (!$this->dirty) {
			$this->dirtyColumns = array();
		}
	}

	public function isDirty($column = null) {
		if (is_null($column)) {
			return $this->dirty;
		} else {
			return array_key_exists($column, $this->dirtyColumns);
		}
	}

	public function setStored($stored = false) {
		$this->stored = $stored;
	}

	public function isStored() {
		return $this->stored;
	}

	private $reflectionClass;

	public function getReflectionClass() {
		if (! isset($this->reflectionClass)) {
			$this->reflectionClass = new ReflectionClass($this);
		}
		return $this->reflectionClass;
	}

	public function getColumnNames() {
		$rc = $this->getReflectionClass();
		$names = array();
		foreach ($rc->getProperties() as $property) {
			$name = $property->getName();
			switch($name) {
			case 'pdo':
			case 'dirty':
				break;
			default:
				$names[] = $name;
			}
		}
		return $names;
	}

	public function getColumnNamesForSql() {
		$cols = $this->getColumnNames();
		$table = $this->getTableName();
		$colStr = '';
		foreach ($cols as $index => $col) {
			$colStr .= ($index ? ', ' : '') . $table . '.' . $col;
		}
		return $colStr;
	}

	public function getTableName() {
		return strtolower(get_class($this)) . 's';
	}

	protected static function findOneByStatement(ZingPDO $pdo, $statement, $model) {
		if (!$statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		$col = new TObjectCollection($pdo, $statement, $model);
		return $col[0];
	}

	protected static function findAllByStatement(ZingPDO $pdo, $statement, $model) {
		if (!$statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		return new TObjectCollection($pdo, $statement, $model);
	}

	public static function getSqlDate($time = null) {
		if (is_null($time)) {
			$time = time();
		}
		return gmdate('Y-m-d H:i:s', $time);
	}

	public function insert($ignore = false) {

		if ( ! $this->isDirty() ) {
			return;
		}

		$names = $this->getColumnNames();
		$sql = 'insert ' . ($ignore ? 'ignore ' : '') . 'into '.$this->getTableName().' ('.implode(', ',$names).') values (:'.implode(', :',$names).')';
		$statement = $this->pdo->prepare($sql);
		foreach ($names as $name) {
			$statement->bindParam(':'.$name, $this->$name);
		}
		if (! $statement->execute()) {
			throw new TObjectPdoException($statement);
		}

		$this->id = $this->pdo->lastInsertId();

		$this->setDirty(false);
		$this->setStored(true);
	}

	public function update() {

		if ( ! $this->isDirty() ) {
			return;
		}

		if ( ! $this->isStored() ) {
			return $this->insert();
		}

		$names = $this->getColumnNames();
		$sql = 'update '.$this->getTableName().' set ';
		foreach ($names as $index => $name) {
			$sql .= ($index ? ', ' : '') . $name . ' = :' . $name;
		}

		$sql .= ' where id = :object_id';

		$statement = $this->pdo->prepare($sql);
		foreach ($names as $name) {
			$type = PDO::PARAM_STR;
			switch (gettype($this->$name)) {
				case 'boolean':
					$type = PDO::PARAM_BOOL;
					break;
				case 'integer':
					$type = PDF::PARAM_INT;
					break;
			}
			$statement->bindParam(':'.$name, $this->$name, $type);
		}
		$statement->bindParam(':object_id', $this->id);

		if (! $statement->execute()) {
			throw new TObjectPdoException($statement);
		}

		$this->setDirty(false);
		$this->setStored(true);

	}

	public function destroy($cascade = false) {

		if ( ! $this->isStored() ) {
			return;
		}

		$sql = 'delete from '.$this->getTableName().' where id = :id';
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam(':id', $this->id);

		if (! $statement->execute()) {
			throw new TObjectPdoException($statement);
		}
	}

	public function validate() {
		$errors = array();
		$columns = $this->getColumnNames();

		foreach ($this->getReflectionClass()->getProperties() as $property) {
			$name = $property->getName();
			if (!in_array($name, $columns)) {
				// only check properties that are columns
				continue;
			}

			$validate = 'validate'.$name;
			if (method_exists($this, $validate)) {
				$err = $this->$validate();
				if ($err !== true) {
					$errors[$name] = $err;
				}
			} else {
				$result = true;
				if (preg_match_all('/@validate\s+(?:(?P<optional>optional)\s+)?\"(?P<error>[^\"]*)\"\s+(?P<method>\w+)(?:\s+(?P<args>[^\\r\\n]+))?/i', $property->getDocComment(), $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						if (strlen($match['optional']) && (strlen($this->$name) == 0)) {
							// not applied if no data
							$result = true;
						} else {
							switch (strtolower($match['method'])) {
							case 'int':
								// int
								$result = strval( (int)$this->$name ) == $this->$name;
								break;
							case 'regexp':
								// regexp <exp>
								$result = preg_match($match['args'], $this->$name) ? true : false;
								break;
							case 'min':
								// min value
								$result = $this->$name >= (int)$match['args'] ? true : false;
								break;
							case 'max':
								// max value
								$result = $this->$name <= (int)$match['args'] ? true : false;
								break;
							case 'minlen':
								// minlen len
								$result = strlen($this->$name) >= (int)$match['args'] ? true : false;
								break;
							case 'maxlen':
								// maxlen len
								$result = strlen($this->$name) <= (int)$match['args'] ? true : false;
								break;
							case 'length':
								// length len
								$result = strlen($this->$name) == (int)$match['args'] ? true : false;
								break;
							case 'postcode':
								// postcode
								$result = preg_match('/^[A-PR-UWYZ][A-HK-Y0-9][A-HJKSTUW0-9]?[ABEHMNPRVWXY0-9]? {1,2}[0-9][ABD-HJLN-UW-Z]{2}$/', $this->$name) ? true : false;
								break;
							case 'email':
								// email
								$result = preg_match('/^[A-Z0-9\-_]+(\.[A-Z0-9\-_&]+)*@[A-Z0-9-]+(\.[A-Z0-9-]+)+$/i', $this->$name) ? true : false;
								break;
							case 'url':
								// url
								$result = preg_match('/^([A-Z]+:\/\/)?[A-Z0-9\-_]+(\.[A-Z0-9\-_]+)+(:\d+)?(\/[^\/\?]+)*(\?.*)?$/i', $this->$name) ? true : false;
								break;
							case 'date_ymd':
								// date_ymd
								$result = preg_match('/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/i', $this->$name) ? true : false;
								break;
							case 'sql_datetime':
								$result = preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $this->$name) ? true : false;
								break;
							case 'wordcount':
								// wordcount min,max
								list($min, $max) = explode(',',$match['args']);
								$wc = str_word_count($this->$name);
								$result = ($wc <= $max && $wc >= $min) ? true : false;
								break;
							case 'ip_address':
								$result = preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $this->$name, $matches) ? true : false;
								break;
							default:
								throw new exception('Unknown validation method \''.$match['method'].'\'');
							}
						}

						if ($result == false) {
							if (strlen($match['error'])) {
								$errors[$name] = $match['error'];
							} else {
								$errors[$name] = 'Invalid data';
							}
							break;	// stop validating once it fails
						}
					}
				}
			}
		}

		return count($errors) ? $errors : true;
	}

	public function isEqual($object) {
		if (get_class($this) != get_class($object)) {
			return false;
		}

		foreach ($this->getColumnNames() as $column) {
			if ($this->$column != $object->$column) {
				return false;
			}
		}

		return true;
	}

	public function reloadDynamicValue($column) {
		unset($this->columns[$column]);
	}

	public function getDirtyColumns($alt = true) {
		if (!$alt) {
			return array_keys($this->dirtyColumns);
		}

		$dirtyColumns = array();

		foreach ($this->getReflectionClass()->getProperties() as $property) {
			$name = $property->getName();
			// only convert properties that are in the dirty columns array
			if (array_key_exists($name, $this->dirtyColumns)) {
				if (preg_match_all('/@alt\s+(?P<alt>.*)/i', $property->getDocComment(), $m)) {
					$dirtyColumns[] = $m['alt'][0];
				} else {
					$dirtyColumns[] = $name;
				}
			}
		}
		return $dirtyColumns;
	}

}



?>
