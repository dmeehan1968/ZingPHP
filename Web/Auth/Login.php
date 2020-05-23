<?php

class Login extends TTemplateControl {

	public function load() {

		$sess = TSession::getInstance();
		if (empty($sess->app->request->referer) || stristr($sess->app->server->referer, $sess->app->server->path_info) !== false) {
			$this->referer->setValue(strcasecmp($sess->app->server->request_uri,'/login') == 0 ? '/' : $sess->app->server->request_uri);
		} else {
			$this->referer->setValue($sess->app->request->referer);
		}
		if (!empty($sess->app->request->rc)) {
			$this->divNotify->setNotification(false, $this->authManager->getReason($sess->app->request->rc));
		}
	}

	public function doLogin($control, $params) {

		$sess = TSession::getInstance();
		if (($rc = $this->authManager->authenticate($sess->parameters->pdo, $this->username->getValue(), $this->password->getValue())) == TAuthentication::RC_NO_ERROR) {
			header('Location:'.$this->referer->getValue());
			exit;
		} else {
			$this->divNotify->setNotification(false, 'Authentication failed.  ' . $this->authManager->getReason($rc));
		}
	}

	public function doReset($control, $params) {
		$authuser = AuthUser::findOneByUsername($this->session->parameters->pdo, $this->session->app->request->username);
		if (! $authuser) {
			$this->divNotify->setNotification(false, 'No account is known for the specified username.  Unable to reset password.');
		} else {
			$res = $authuser->resetPassword(array(	'verifyModule' => $this->getVerifyModule(),
											'protocol' => $this->session->parameters['site.protocol'],
											'host' => $this->session->parameters['site.host'],
											'sitename' => $this->session->parameters['site.realname']));
			if (PEAR::isError($res)) {
				$this->divNotify->setNotification(false, 'There was an error resetting the password for the specified account.  The specific error message was "' . $res->getMessage() . '"');
			} else if ($res != TAuthentication::RC_NO_ERROR) {
				$this->divNotify->setNotification(false, 'The password cannot be reset as the account has expired.  Please contact the site administrator.');
			} else {
				$this->divNotify->setNotification(true, 'An account verification email has been sent to the specified user.  Please check the email account and follow the account verification instructions.');
			}
		}
	}

	private $verifyModule = 'Zing/Web/Auth/THtmlAuthVerify';

	public function setVerifyModule($mod) {
		$this->verifyModule = $mod;
	}

	public function getVerifyModule() {
		return $this->verifyModule;
	}

	private $useFormsCss = true;

	public function setUseFormsCss($use) {
		$this->useFormsCss = zing::evaluateAsBoolean($use);
	}

	public function getUseFormsCss() {
		return $this->useFormsCss;
	}

	private $title = "Login";

	public function setTitle($title) {
		$this->title = $title;
	}

	public function preRender() {
//		$this->loginTitle->children->deleteAll();
		$this->loginTitle->setInnerText($this->title);

		$this->cssForms->setVisible($this->useFormsCss);
		parent::preRender();
	}
}

?>
