<?php

class Logout extends TControl {

	public function init() {
		$auth = TAuthentication::getInstance();
		$auth->logout();
		$sess = TSession::getInstance();

		header('Location: /');
		exit;
	}

}

?>
