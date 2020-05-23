<?php

class TAuthCondition extends TCompositeControl {

	public function render() {
		$render = true;

		if ($this->hasUsers()) {
			if (array_search($this->auth->getUsername(), $this->getUsers()) === false) {
				$render = false;
			}
		}

		if ($this->hasGroups()) {

			foreach ($this->getGroups() as $group) {
				if (array_search($group, $this->auth->getUserGroups()) === false) {
					$render = false;
				}
			}
		}

		if ($this->hasRoles()) {
			foreach ($this->getRoles() as $role) {
				if (array_search($role, $this->auth->getUserRoles()) === false) {
					$render = false;
				}
			}
		}

		if ($this->hasPermissions()) {
			foreach ($this->getPermissions() as $perm) {
				if (array_search($perm, $this->auth->getUserPermissions()) === false) {
					$render = false;
				}
			}
		}

		if ($this->hasGuest()) {
			if ($this->getGuest() != $this->auth->isGuest()) {
				$render = false;
			}
		}

		if ($render) {
			parent::render();
		}

	}

	private $users = array();

	public function setUser($user) {
		$this->users[] = $user;
	}

	public function getUsers() {
		return $this->users;
	}

	public function hasUsers() {
		return (bool) count($this->users);
	}

	private $groups = array();

	public function setGroup($group) {
		$this->groups[] = $group;
	}

	public function getGroups() {
		return $this->groups;
	}

	public function hasGroups() {
		return (bool) count($this->groups);
	}

	private $roles = array();

	public function setRole($role) {
		$this->roles[] = $role;
	}

	public function getRoles() {
		return $this->roles;
	}

	public function hasRoles() {
		return (bool) count($this->roles);
	}

	private $permissions = array();

	public function setPermission($perm) {
		$this->permissions[] = $perm;
	}

	public function getPermissions() {
		return $this->permissions;
	}

	public function hasPermissions() {
		return (bool) count($this->permissions);
	}

	private $guest;

	public function setGuest($guest) {
		$this->guest = zing::evaluateAsBoolean($guest);
	}

	public function getGuest() {
		return $this->guest;
	}

	public function hasGuest() {
		return isset($this->guest);
	}
}

?>
