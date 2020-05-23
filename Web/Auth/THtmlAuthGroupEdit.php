<?php

class THtmlAuthGroupEdit extends TModule {

	public function auth() {
		$this->setAuthPerms('AuthGroupEdit');
		parent::auth();
	}

	public function load() {

		$sess = TSession::getInstance();
		$id = $sess->app->request->group_id;

		$this->group = AuthGroup::findById($sess->parameters->pdo, $id);
        if (empty($this->group)) {
			throw new Exception('404 Not Found');
		}

		$this->groupTitle->setInnerText('Edit ACL for Group "' . $this->group->name . '"');

		$roles = AuthRole::findAll($sess->parameters->pdo);
		$this->roles->setAvailableBoundObject($roles);
		$this->roles->setAvailableBoundProperty('id|name');

		$perms = AuthPerm::findAll($sess->parameters->pdo);
		$this->permissions->setAvailableBoundObject($perms);
		$this->permissions->setAvailableBoundProperty('id|name');

		parent::load();
	}

	public function preRender() {

		$this->roles->setAssignedBoundObject($this->group->roles);
		$this->roles->setAssignedBoundProperty('id|name');

		$this->permissions->setAssignedBoundObject($this->group->permissions);
		$this->permissions->setAssignedBoundProperty('id|name');

		parent::preRender();
	}

	public function onAddRoles($control, $params) {
		if (count($params)) {
			$this->group->addRoleById($params);
		}
	}

	public function onRemoveRoles($control, $params) {
		if (count($params)) {
			$this->group->removeRoleById($params);
		}
	}

	public function onAddPermissions($control, $params) {
		if (count($params)) {
			$this->group->addPermissionById($params);
		}
	}

	public function onRemovePermissions($control, $params) {
		if (count($params)) {
			$this->group->removePermissionById($params);
		}
	}

	public function insertExpandedPermissions($control, $params) {
		foreach ($this->group->expandedPermissions as $index => $perm) {
			$control->setInnerText($control->getInnerText() . ($index ? ', ' : '') . $perm);
		}
	}


}

?>
