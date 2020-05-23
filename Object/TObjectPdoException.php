<?php

class TObjectPdoException extends Exception {

	public function __construct(PDOStatement $statement) {
		$err = $statement->errorInfo();
		parent::__construct($err[2], $err[0]);
	}

}

?>
