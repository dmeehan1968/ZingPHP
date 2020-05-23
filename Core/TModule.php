<?php

class TModule extends TTemplateControl {

	public function auth() {
		parent::auth();

		$sess = TSession::getInstance();

		if (! $this->hasPermission()) {
			$this->authManager->login($sess->app->server->request_uri, TAuthentication::RC_NOT_AUTHORISED);
		}
	}
}

?>
