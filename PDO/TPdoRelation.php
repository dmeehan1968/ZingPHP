<?php

class TPdoRelation { 

	private $name;
	private $class;
	private $magnitude;
	private $select;
		
	const RELATION_STATEMENT = '/has\s+(?P<magnitude>one|many)\s+(?P<class>\w+)|(?P<select>select[^\n]+)/i';
	
	public function __construct($name, $expr = null) {
		$this->name = $name;
		if (isset($expr)) {
			$this->interpret($expr);
		}	
	}
	
	public function interpret($expr) {

		if (preg_match_all(self::RELATION_STATEMENT, $expr, $matches, PREG_SET_ORDER)) {
			foreach ($matches[0] as $property => $value) {

				if (is_int($property) || empty($value)) {
					continue;
				}
				
				switch ($property) {
				case 'magnitude':
					$this->setMagnitude(strcasecmp($value, 'many') == 0);
					break;
				case 'class':
					$this->setClass($value);
					break;
				case 'select':
					$this->setSelect($value);
					break;
				}
			}
		} else {
			throw new Exception('unrecognised @sql expression: \''.$expr.'\'');
		}

	}
	
	public function setClass($class) {
		$this->class = $class;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function setMagnitude($mag) {
		$this->magnitude = $mag;
	}
	
	public function getMagnitude() {
		return $this->magnitude;
	}

	public function hasMany() {
		return $this->getMagnitude() == true;
	}
	
	public function hasOne() {
		return $this->getMagnitude() == false;
	}
	
	public function setSelect($select) {
		$this->select = $select;
	}
	
	public function getSelect($params) {
		$select = $this->select;
		
		if (preg_match_all('/{(?:(?P<param>\w+)|(?P<object>\w+)(?:->(?P<prop>\w+))+)}/i', $select, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) > 0) {
			$adjust = 0;
			foreach ($matches as $match) {
				$paramIndex = $match['param'][0];
				if (!empty($paramIndex)) {
					$replace = $params[$paramIndex];
				} else {
					$paramIndex = $match['object'][0];
					if (!empty($paramIndex)) {
						$property = $match['prop'][0];
						$replace = $params[$paramIndex]->$property;
					}
				}
				
				$offset = $match[0][1] + $adjust;
				$length = strlen($match[0][0]);
				$select = substr_replace($select, $replace, $offset, $length);
				$adjust += strlen($replace) - strlen($match[0][0]);
					
			}
		}
		return $select;
	}
	
}

?>