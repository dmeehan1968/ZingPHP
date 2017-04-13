<?php

class TPdoColumn {

	public $name = 'unknown';
	public $type = 'int';
	public $size = 12;	
	public $notNull = false;
	public $autoIncrement = false;
	public $default = null;
	
	const COLUMN_STATEMENT = '/(?:(int|char)\((\d+)\)?)|(not null)|(auto_increment)|default\s+([^)]+)/i';

	public function __construct($name, $expr = null) {
		$this->name = $name;
		if (isset($expr)) {
			$this->interpret($expr);
		}
	}

	public function interpret($expr) {

		preg_match_all(self::COLUMN_STATEMENT, $expr, $attrs, PREG_SET_ORDER);

		foreach ($attrs as $attr) {

			if (!empty($attr[1])) { // type
				$this->type = $attr[1];
			}
			
			if (!empty($attr[2])) { // size
				$this->size = (float)$attr[2];
			}
			
			if (!empty($attr[3])) { // not null
				$this->notNull = true;
			}
			
			if (!empty($attr[4])) { // auto_increment
				$this->autoIncrement = true;
			}
			
			if (!empty($attr[5])) { // default
				$this->default = $attr[5];
			}
		}						
	}
		
}

?>