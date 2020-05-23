<?php

class THtmlAuthUserEdit extends TModule {

    public function auth() {
        $this->setAuthPerms('AuthUserEdit');
        parent::auth();
    }

    public function load() {

        $sess = TSession::getInstance();

        if ($sess->app->request->user_id == 'new') {
            $this->user = new AuthUser($sess->parameters->pdo);
            $this->UserTitle->setInnerText('Create User');
			$this->btnReset->setDisabled(true);
        } else {
            $this->user = AuthUser::findOneById($sess->parameters->pdo, $sess->app->request->user_id);
            if (empty($this->user)) {
                throw new Exception('404 Not Found');
            }
            $this->UserTitle->setInnerText('Edit User: "' . $this->user->username . '"');
        }

		$groups = AuthGroup::findAll($sess->parameters->pdo);
		$this->Groups->setAvailableBoundObject($groups);
		$this->Groups->setAvailableBoundProperty('id|name');

		$roles = AuthRole::findAll($sess->parameters->pdo);
		$this->Roles->setAvailableBoundObject($roles);
		$this->Roles->setAvailableBoundProperty('id|name');

		$permissions = AuthPerm::findAll($sess->parameters->pdo);
		$this->Permissions->setAvailableBoundObject($permissions);
		$this->Permissions->setAvailableBoundProperty('id|name');

        $this->setBoundObject($this->user);

		if ($this->user->isStored() == false) {
			$this->frmAuthUserPermissions->setVisible(false);
		}

        parent::load();
    }

	public function preRender() {
		$this->Groups->setAssignedBoundObject($this->user->groups);
		$this->Groups->setAssignedBoundProperty('id|name');

		$this->Roles->setAssignedBoundObject($this->user->roles);
		$this->Roles->setAssignedBoundProperty('id|name');

		$this->Permissions->setAssignedBoundObject($this->user->permissions);
		$this->Permissions->setAssignedBoundProperty('id|name');

		if ($this->user->isStored() && ! $this->divNotify->hasNotification()) {
			if ($this->session->app->request->saved) {
				$this->divNotify->setNotification(true, 'The user has been created, and an account verification message sent.');
			} else if (! $this->user->isVerified()) {
				$this->divNotify->setNotification('notification-warning', 'This account is awaiting verification');
			} else if ($this->user->isExpired()) {
				$this->divNotify->setNotification('notification-warning', 'This account has expired');
			}
		}
		parent::preRender();
	}


    public function saveUser($control, $params) {

        $sess = TSession::getInstance();
        $request = $sess->app->request;
        $errors = array();
        $newUser = ! $this->user->isStored();

        try {

            $sess->parameters->pdo->beginTransaction();

            $this->user->username = $request->username;
            $this->user->expires = $request->expires;

            if (($e = $this->user->validate()) !== true) {
                $errors = array_merge($errors, $e);
            }

			if (count($errors)) {
                throw new Exception('There are '.count($errors).' problems with the information you have provided.  Please make the changes indicated to allow the information to be saved');
			}

			if ($newUser) {

				$res = $this->user->resetPassword(				// also saves
									   array(
											 'protocol' => $this->session->parameters['site.protocol'],
											 'host' => $this->session->parameters['site.host'],
											 'sitename' => $this->session->parameters['site.realname'],
											 'verifiedExpires' => new TDateTime($this->user->expires)
											 ));
				if (PEAR::isError($res)) {
					throw new Exception($res->getMessage());
				}

				if ($res != TAuthentication::RC_NO_ERROR) {
					throw new Exception(TAuthentication::getReason($res));
				}

			} else {
				$this->user->update();
			}

			$this->user->addGroupByName($sess->parameters['auth.default.groups']);

            $sess->parameters->pdo->commit();

            if ($newUser) {
                $sess->app->redirect('Zing/Web/Auth/THtmlAuthUserEdit', array('user_id' => $this->user->id), array('saved' => true));
            }

            $this->divNotify->setNotification(true, 'Changes to the User have been saved');

        } catch (Exception $e) {
            $sess->parameters->pdo->rollback();

            foreach ($errors as $property => $error) {
				if ($this->$property) {
					$this->$property->setError($error);
				}
            }
            $this->divNotify->setNotification(false, $e->getMessage());
        }

        $this->divNotify->setVisible(true);
    }

	public function resetPassword($control, $params) {
		$res = $this->user->resetPassword(
								   array(
										 'protocol' => $this->session->parameters['site.protocol'],
										 'host' => $this->session->parameters['site.host'],
										 'sitename' => $this->session->parameters['site.realname'],
										 'verifiedExpires' => new TDateTime($this->user->expires)
										 ));
		if (PEAR::isError($res)) {
			$this->divNotify->setNotification(false, 'There was an error mailing the user the account verification message.  The specific error was "' . $res->getMessage() . '"');
		} else if ($res != TAuthentication::RC_NO_ERROR) {
			$this->divNotify->setNotification(false, 'There was an error resetting the password. ' . TAuthentication::getReason($res));
		} else {
			$this->divNotify->setNotification(true, 'The password has been reset and an account verification message sent.');
		}
	}

	public function onAddGroups($control, $params) {
		if (count($params)) {
			$this->user->addGroupById($params);
		}
	}

	public function onRemoveGroups($control, $params) {
		if (count($params)) {
			$this->user->removeGroupById($params);
		}
	}

	public function onAddRoles($control, $params) {
		if (count($params)) {
			$this->user->addRoleById($params);
		}
	}

	public function onRemoveRoles($control, $params) {
		if (count($params)) {
			$this->user->removeRoleById($params);
		}
	}

	public function onAddPermissions($control, $params) {
		if (count($params)) {
			$this->user->addPermissionById($params);
		}
	}

	public function onRemovePermissions($control, $params) {
		if (count($params)) {
			$this->user->removePermissionById($params);
		}
	}

	public function insertExpandedGroups($control, $params) {
		foreach ($this->user->expandedGroups as $index => $group) {
			$control->setInnerText($control->getInnerText() . ($index ? ', ' : '') . $group);
		}
	}

	public function insertExpandedRoles($control, $params) {
		foreach ($this->user->expandedRoles as $index => $role) {
			$control->setInnerText($control->getInnerText() . ($index ? ', ' : '') . $role);
		}
	}

	public function insertExpandedPermissions($control, $params) {
		foreach ($this->user->expandedPermissions as $index => $perm) {
			$control->setInnerText($control->getInnerText() . ($index ? ', ' : '') . $perm);
		}
	}
}


?>
