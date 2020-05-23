<?php

class THtmlAuthRoleEdit extends TModule {

	public function auth() {
		$this->setAuthPerms('AuthRoleEdit');
		parent::auth();
	}

	public function load() {

		$sess = TSession::getInstance();
		$id = $sess->app->request->role_id;

		$this->role = AuthRole::findById($sess->parameters->pdo, $id);
        if (empty($this->role)) {
			throw new Exception('404 Not Found');
		}

		$this->roleTitle->setInnerText('Edit ACL for Role "' . $this->role->name . '"');

		$perms = AuthPerm::findAll($sess->parameters->pdo);
		$this->permissions->setAvailableBoundObject($perms);
		$this->permissions->setAvailableBoundProperty('id|name');

		parent::load();
	}

	public function preRender() {

		$this->permissions->setAssignedBoundObject($this->role->permissions);
		$this->permissions->setAssignedBoundProperty('id|name');

		parent::preRender();
	}

	public function onAddPermissions($control, $params) {
		if (count($params)) {
			$this->role->addPermissionById($params);
		}
	}

	public function onRemovePermissions($control, $params) {
		if (count($params)) {
			$this->role->removePermissionById($params);
		}
	}

}

?>
