<?php

class THtmlAuthUserList extends TModule {

	public function auth() {
		$this->setAuthPerms('AuthUserRead');
		parent::auth();
	}

	public function preRender() {

		$sess = TSession::getInstance();

		$users = AuthUser::findAll($sess->parameters->pdo);
		$this->setBoundObject($users);

		parent::preRender();
	}

	public function insertCheckbox($control, $params) {
		$user = $control->getBoundObject();
		$params = array(	'id' => 'usersToDelete[]',
								'value' => $user->id,
							);
		$control->children->deleteAll();
		$child = $control->children[] = zing::create('THtmlCheckbox', $params);
		$child->doStatesUntil('preRender');
	}

	public function insertUserLink($control, $params) {
		if ($this->authManager->hasPerm('AuthUserEdit')) {
			$user = $control->getBoundObject();
			$params = array(	'module' => 'Zing/Web/Auth/THtmlAuthUserEdit',
									'user_id' => $user->id,
									'innerText' => $control->getInnerText()
								);
			$control->children->deleteAll();
			$child = $control->children[] = zing::create('THtmlLink', $params);
			$child->doStatesUntil('preRender');
		}
	}

	public function insertStatus($control, $params) {
		$authuser = $control->getBoundObject();
		$control->children->deleteAll();
		if (! $authuser->isVerified()) {
			$control->setInnerText('Awaiting Verification');
		} else if ($authuser->isExpired()) {
			$control->setInnerText('Expired');
		} else {
			$control->setInnerText('Active');
		}
	}

	public function addUser($control, $params) {
		$sess = TSession::getInstance();
		$sess->app->redirect('Zing/Web/Auth/THtmlAuthUserEdit', array('user_id' => 'new'));
	}

	public function deleteUsers($control, $params) {
		$sess = TSession::getInstance();
		$count = 0;
		foreach ((array)$sess->app->request->usersToDelete as $user_id) {
			if ($user = AuthUser::findOneById($sess->parameters->pdo, $user_id)) {
				$user->destroy(true);
				$count++;
			}
		}

		$this->divNotify->setNotification($count ? true : false, $count . ' users deleted');
	}

	public function insertAuthNames($control, $authName) {
		$control->children->deleteAll();
		$user = $control->getBoundObject();
		foreach ($user->$authName as $name) {
			$control->children[] = zing::create('THtmlDiv', array('innerText' => $name->name));
		}
	}

	public function insertUserGroups($control, $params) {
		$this->insertAuthNames($control, 'groups');
	}

	public function insertUserRoles($control, $params) {
		$this->insertAuthNames($control, 'roles');
	}

	public function insertUserPermissions($control, $params) {
		$this->insertAuthNames($control, 'permissions');
	}
}

?>
