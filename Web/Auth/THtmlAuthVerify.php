<?php

class THtmlAuthVerify extends TModule {

	public function load() {

		$this->authManager->logout();	// log out any existing session before berifiyng
		
		$authuser = AuthUser::findOneByCredentials($this->session->parameters->pdo,
											   $this->session->app->request->username,
											   $this->session->app->request->password);
		
		if ($authuser) {
			$this->setAuthUser($authuser);
		} else {
			$this->frmVerifyAccount->setVisible(false);
			$this->divInvalidVerificationCode->setVisible(true);
		}
	}
	
	public $useFormsCSS = true;
	
	public function setUseFormsCSS($use) {
		$this->useFormsCSS = zing::evaluateAsBoolean($use);
	}
	
	public function getUseFormsCSS() {
		return $this->useFormsCSS;
	}
	
	private $authUser;
	
	public function setAuthUser($authuser) {
		$this->authUser = $authuser;
		$this->setBoundObject($authuser);
	}
	
	public function getAuthUser() {
		return $this->authUser;
	}
	
	public function hasAuthUser() {
		return isset($this->authUser);
	}
	
	private $autoLogin = true;
	
	public function setAutoLogin($auto) {
		$this->autoLogin = $auto;
	}
	
	public function getAutoLogin() {
		return $this->autoLogin;
	}
	
	public function preRender() {
		$this->cssForms->setVisible($this->getUseFormsCSS());
		parent::preRender();
	}
	
	public function setUsername($username) {
		$this->username->setValue($username);
	}
	
	public function render() {
		if ($this->username->getValue() == '' && $this->hasAuthUser()) {
			$this->username->setValue($this->getAuthUser()->username);
		}
		parent::render();
	}
	
	public function onVerify($control, $params) {

		if ($this->hasAuthUser()) {
			$password = $this->session->app->request->password1;
			if ($password != $this->session->app->request->password2) {
				$this->divNotify->setNotification(false,'The passwords supplied do not match');
			} else if (isset($this->session->parameters['auth.password.min']) && strlen($password) < $this->session->parameters['auth.password.min']) {
				$this->divNotify->setNotification(false, 'Passwords must be at least ' . $this->session->parameters['auth.password.min'] . ' characters in length');
			} else if (isset($this->session->parameters['auth.password.min.digits']) && ($cnt=preg_match_all('/\d/', $password, $notused)) < $this->session->parameters['auth.password.min.digits']) {
				$this->divNotify->setNotification(false, 'Passwords must contain at least ' . $this->session->parameters['auth.password.min.digits'] . ' numbers');
			} else if (isset($this->session->parameters['auth.password.min.alpha']) && preg_match_all('/[A-Z]\w/i', $password, $notused) < $this->session->parameters['auth.password.min.alpha']) {
				$this->divNotify->setNotification(false, 'Passwords must contain at least ' . $this->session->parameters['auth.password.min.alpha'] . ' characters');
			} else {
				$authuser = $this->getAuthUser();
				$authuser->verifyUser($password, $this->session->app->server->remote_addr);
				
				if ($this->getAutoLogin()) {			
					if ($rc = $this->authManager->authenticate($this->session->parameters->pdo, $this->session->app->request->username, $password)) {
						$this->divNotify->setNotification(false, 'Verification succeeded, but could not authenticate.  ' . $this->authManager->getReason($rc));
						return;
					}
				}
				$this->frmVerifyAccount->setVisible(false);
				$this->divPostVerify->setVisible(true);
			}
		}
	}
	
	private $homepageUrl = '/';
	
	public function setHomepageUrl($url) {
		$this->homepageUrl = $url;
	}
	
	public function getHomepageUrl() {
		return $this->homepageUrl;
	}
	
	public function setHomepageAddress($control, $params) {
		$control->setHref($this->getHomepageUrl());
	}

}

?>