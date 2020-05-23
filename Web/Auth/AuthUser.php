<?php

require_once('Mail.php');

class AuthUser extends TObjectPersistence {

	public	$id;
	/**
	 * @validate "Usernames must be a valid email address" email
	 */
	public	$username;				// username should be email address
	public 	$password;

	/**
	 * @validate "You must enter a valid date/time" sql_datetime
	 */
	public	$created;				// when this account was created
	/**
	 * @validate "You must enter a valid date/time" sql_datetime
	 */
	public	$expires;				// when this account expires
	/**
	 * @validate optional "Should be a valid IP address" ip_address
	 */
	public	$verified_ip;			// the ip address of the client that verifies
	/**
	 * @validate optional "You must enter a valid date/time" sql_datetime
	 */
	public	$verified_timestamp;	// the date/time of when the verification occured
	/**
	 * @validate optional "You must enter a valid date/time" sql_datetime
	 */
	public	$verified_expires;		// the new expiry time after verification
	/**
	 * @validate optional "You must enter a valid date/time" sql_datetime
	 */
	public	$last_login;			// the datetime of the last login
	/**
	 * @validate optional "You must enter a valid date/time" sql_datetime
	 */
	public	$previous_login;		// the datetime of the previous login

	public function __construct(ZingPDO $pdo, $params = array()) {
		parent::__construct($pdo);
		$created = new TDateTime();
		$expires = new TDateTime(-1);
		$this->created = $created->__toString();
		$this->expires = $expires->__toString();
		$this->processParams($params);
		$this->setDirty(false);
	}

	public function loadGroups() {
		$sql = '	select authgroups.* from authgroups
					join authuser_relatesto_authgroups as auag on auag.authgroup_id = authgroups.id
					where auag.authuser_id = :id
					order by authgroups.name';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($this->pdo, $s, 'AuthGroup');
	}

	public function loadRoles() {
		$sql = '	select authroles.* from authroles
					join authuser_relatesto_authroles as auar on auar.authrole_id = authroles.id
					where auar.authuser_id = :id
					order by authroles.name';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($this->pdo, $s, 'AuthRole');
	}

	public function loadPermissions() {
		$sql = '	select authperms.* from authperms
					join authuser_relatesto_authperms as auap on auap.authperm_id = authperms.id
					where auap.authuser_id = :id
					order by authperms.name';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($this->pdo, $s, 'AuthPerm');
	}

	public function loadExpandedGroups() {
		$groups = array();
		foreach ($this->groups as $group) {
			$groups[] = $group->name;
		}
		sort($groups);
		return $groups;
	}

	public function loadExpandedRoles() {
		$roles = array();
		foreach ($this->groups as $group) {
			foreach ($group->roles as $role) {
				$roles[] = $role->name;
			}
		}
		foreach ($this->roles as $role) {
			$roles[] = $role->name;
		}
		$roles = array_unique($roles);
		sort($roles);
		return $roles;
	}

	public function loadExpandedPermissions() {
		$perms = array();
		foreach ($this->groups as $group) {
			foreach ($group->roles as $role) {
				foreach ($role->permissions as $perm) {
					$perms[] = $perm->name;
				}
			}

			foreach ($group->permissions as $perm) {
				$perms[] = $perm->name;
			}
		}

		foreach ($this->permissions as $perm) {
			$perms[] = $perm->name;
		}

		$perms = array_unique($perms);
		sort($perms);
		return $perms;
	}

	public static function findOneByCredentials(ZingPDO $pdo, $username, $password) {
		$sql = '	select * from authusers
					where username = :username and password = md5(:password)
					limit 1';
		$s = $pdo->prepare($sql);
		$s->bindParam(':username', $username, ZingPDO::PARAM_STR);
		$s->bindParam(':password', $password, ZingPDO::PARAM_STR);
		if (! $s->execute()) {
			throw new TObjectPdoException($s);
		}
		$col = new TObjectCollection($pdo, $s, 'AuthUser');
		return $col[0];
	}

	public static function findOneById(ZingPDO $pdo, $id) {
		$sql = '	select * from authusers
					where id = :id
					limit 1';
		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $id, ZingPDO::PARAM_INT);
		if (! $s->execute()) {
			throw new TObjectPdoException($s);
		}
		$col = new TObjectCollection($pdo, $s, 'AuthUser');
		return $col[0];
	}

	public static function findOneByUsername(ZingPDO $pdo, $username) {
		$sql = '	select * from authusers
					where username = :username
					limit 1';
		$s = $pdo->prepare($sql);
		$s->bindParam(':username', $username, ZingPDO::PARAM_STR);
		if (! $s->execute()) {
			throw new TObjectPdoException($s);
		}
		$col = new TObjectCollection($pdo, $s, 'AuthUser');
		return $col[0];
	}

	public static function findAll(ZingPDO $pdo) {
		$sql = 'select * from authusers order by username';
		$s = $pdo->prepare($sql);
		if (! $s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($pdo, $s, 'AuthUser');
	}

	public function addGroupById($groups = array()) {
		$sql = 'insert ignore into authuser_relatesto_authgroups (authuser_id, authgroup_id)
				select :user, id from authgroups where id in (' . implode(',', (array)$groups) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$this->reloadDynamicValue('groups');
	}

	public function removeGroupById($groups = array()) {
		$sql = 'delete from authuser_relatesto_authgroups where authuser_id = :user and authgroup_id = :group';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		$s->bindParam(':group', $group, ZingPDO::PARAM_INT);
		foreach ((array)$groups as $group) {
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}
		}
	}

	public function addGroupByName($groups = array()) {
		$groupText = '';
		foreach ((array)$groups as $group) {
			$groupText .= (!empty($groupText) ? ', ' : '') . $this->pdo->quote($group);
		}

		$sql = 'insert ignore into authuser_relatesto_authgroups (authuser_id, authgroup_id)
				select :user, id from authgroups where name in (' . $groupText . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$this->reloadDynamicValue('groups');
	}

	public function addRoleById($roles = array()) {
		$sql = 'insert ignore into authuser_relatesto_authroles (authuser_id, authrole_id)
				select :user, id from authroles where id in (' . implode(',', (array)$roles) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$this->reloadDynamicValue('roles');
	}

	public function removeRoleById($roles = array()) {
		$sql = 'delete from authuser_relatesto_authroles where authuser_id = :user and authrole_id in (' . implode(',',(array)$roles) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$this->reloadDynamicValue('roles');
	}

	public function addPermissionById($perms = array()) {
		$sql = 'insert ignore into authuser_relatesto_authperms (authuser_id, authperm_id)
				select :user, id from authperms where id in (' . implode(',', (array)$perms) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$this->reloadDynamicValue('permissions');
	}

	public function removePermissionById($perms = array()) {
		$sql = 'delete from authuser_relatesto_authperms where authuser_id = :user and authperm_id in (' . implode(',',(array)$perms) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':user', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$this->reloadDynamicValue('permissions');
	}

	public function destroy($cascade = false) {
		if ($cascade) {
			$sql = 'delete from authuser_relatesto_authgroups where authuser_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authuser_relatesto_authroles where authuser_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authuser_relatesto_authperms where authuser_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}
		}
		parent::destroy($cascade);
	}

	public static function deleteOneById(ZingPDO $pdo, $id) {
		$sql = 'delete from authusers where id = :id';
		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return true;
	}

	/**
	 * resetPassword
	 *
	 * Parameters are:
	 *
	 * passwordLength	The number of characters in the auto generated password
	 * verifyModule		The module path used as the verification URL
	 * username			The username to display in the verification greeting
	 * protocol			The protocol to use n the verificationm URL
	 * host				The server address to use in the verification URL
	 * sitename			The real name of the site (stylised domain?)
	 * verifyPeriod		The period in seconds before the account expires whilst
	 * 					waiting for verification
	 * verifiedExpires	TDateTime object containing expiry time
	 *
	 * Returns:
	 *
	 * TAuthentication::RC_xx constant indicating no error, or error code
	 * or
	 * PEAR_Error in the event of a mailing problem.
	 */

	public function resetPassword($userParams = array()) {

		if ($this->expires && $this->isExpired()) {

			return TAuthentication::RC_EXPIRED;
		}
		$sess = TSession::getInstance();

		$verifyPeriod = new TDateTime();
		$verifyPeriod->adjust(0, 0, 14);
		$params = array('passwordLength' => 8,
						'verifyModule' => 'Zing/Web/Auth/THtmlAuthVerify',
						'username' => $this->username,
						'protocol' => 'http://',
						'host' => $sess->app->server->http_host,
						'sitename' => $sess->app->server->http_host,
						'verifyPeriod' => $verifyPeriod,
						'verifiedExpires' => new TDateTime(-1));

		$params = array_merge($params, $userParams);
		$valid = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$password = '';
		$length = $params['passwordLength'];
		while ($length--) {
			$password .= substr($valid, rand(0, strlen($valid)), 1);
		}

		$this->password = md5($password);
		$this->expires = $params['verifyPeriod']->__toString();

		$this->verified_ip = null;
		$this->verified_timestamp = null;
		$this->verified_expires = $params['verifiedExpires']->__toString();

		$mail = Mail::factory($sess->parameters['mail.type'], $sess->parameters['mail.parameters']);

		if (PEAR::isError($mail)) {
			return $mail;
		}
		$link = $params['protocol'] . $params['host'] . $sess->app->getModuleUri($params['verifyModule'], array('username' => $this->username, 'password' => $password));

		$message = <<<EOT
Hello {$params['username']},

You are being sent this email because you (or someone pretending to be you has) requested a
verification of your account on {$params['sitename']}.

IMPORTANT: If you did not request this email, you must still re-verify your account before you will
be able to login again!

In order to use your account, you must follow the link shown below to verify your account and
create your password.

$link

We hope you enjoy using your account.

Thanks.
EOT;


		$res = $mail->send($this->username,
				   array(	'From' => $sess->parameters['mail.parameters']['reply-to'],
							'To' => $this->username,
							'Subject' => $params['sitename'] . ': Account Verification'),
				   $message);

		if (PEAR::isError($res)) {
			return $res;
		}

		$this->update();
		return TAuthentication::RC_NO_ERROR;
	}

	public function verifyUser($password, $remote_addr) {
		$this->password = md5($password);
		$this->verified_ip = $remote_addr;
		$this->verified_timestamp = zing::timeToSqlDateTime();
		$this->expires = $this->verified_expires;
		$this->verified_expires = null;
		$this->update();
	}

	public function recordLogin($time = null) {
		if (is_null($time)) {
			$time = time();
		}

		$this->previous_login = $this->last_login;
		$this->last_login = zing::timeToSqlDateTime($time);
		$this->update();
	}

	public function isVerified() {
		return ! (trim($this->verified_ip) == '');
	}

	public function isExpired() {
		$expires = new TDateTime($this->expires);
		return $expires->lessThan(new TDateTime);
	}
}

?>
