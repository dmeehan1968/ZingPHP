<?php

class ZingPdo {

	private static $tables = array();
	
	public static function createObject($class, PDO $pdo) {
		return new TPdoProxy(self::getTable($class,$pdo));
	}
	
	public static function createCollection($class, PDO $pdo) {
		return new TPdoCollection(self::getTable($class, $pdo));
	}
	
	public static function getTable($class, PDO $pdo) {
		if (isset(self::$tables[$class])) {
			$table = self::$tables[$class];
		} else {
			$table = new TPdoTable($class, $pdo);
			self::$tables[$class] = $table;
		}
		return $table;
	}		
}
	

?>