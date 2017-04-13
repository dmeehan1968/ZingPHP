<?php

class TAuthentication implements ISingleton {

	private $sess;
	
	public function __construct() {
		
		$this->sess = TSession::getInstance();
		$this->init();
	}
	
	private static $instance;
	
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new TAuthentication;
		}

		return self::$instance;
	}

	public function init() {
		session_start();
	}
	
	public function isGuest() {
		return empty($_SESSION['auth']);
	}

	protected function checkMembers($needles, $haystack) {
		$mandatory = false;
		$matches = 0;

		foreach ($needles as $needle) {
			if ($needle[0] == '+') {
				$needle = substr($needle,1);
				$mandatory = true;
			} else {
				$mandatory = false;
			}

			$found = array_search($needle, $haystack);

			if ($found === false && $mandatory) {
				return false;
			}
			
			if ($found !== false) {
				$matches++;
			}
		}
		return ($matches > 0);
	}
	
	public function checkCredentials($groups, $roles, $perms, $guest) {
	
		foreach(array('getUserGroups' => $groups, 'getUserRoles' => $roles, 'getUserPermissions' => $perms) as $method => $items) {
			if (count($items)) {
				$userItems = $this->$method();
				if (! $this->checkMembers($items, $userItems)) {
					return false;
				}
			}
		}

		if (isset($guest)) {	
			return $guest == $this->isGuest();
		}

		return true;
	}

	public function hasGroup($group) {
		return in_array($group, $this->getUserGroups());
	}
	
	public function hasRole($role) {
		return in_array($role, $this->getUserRoles());
	}
	
	public function hasPerm($perm) {
		return in_array($perm, $this->getUserPermissions());
	}
	
	const RC_NO_ERROR = 0;
	const RC_REASON_UNKNOWN = 1;
	const RC_NOT_AUTHORISED = 2;
	const RC_NOT_VERIFIED = 3;
	const RC_EXPIRED = 4;
	const RC_USER_UNKNOWN = 5;
	
	static private $reasons = array(	'Authorisation Successful.',
								'Authorisation Error, Reason Unknown.',
								'You are not authorised to access that resource.',
								'The account has not been verified.',
								'The account has expired.',
								'The specified account is unknown or invalid password.');
	
	public function getReason($reasonCode) {
		if (isset(self::$reasons[$reasonCode])) {
			return self::$reasons[$reasonCode];
		}
		return self::$reasons[self::RC_REASON_UNKNOWN];
	}
	
	public function login($referer = null, $reasonCode = self::RC_REASON_UNKNOWN) {
		$this->sess->app->redirect('Auth/Login', array(), is_null($referer) ? array() : array('referer' => $referer, 'rc' => $reasonCode));
	}
	
	public function logout() {
		session_destroy();
		$this->init();
	}
	
	public function authenticate(ZingPDO $pdo, $username, $password) {
		
		$this->logout();		// make sure we are currently logged out
		
		if ($user = AuthUser::findOneByCredentials($pdo, $username, $password)) {

			if (!$user->isVerified()) {
				return self::RC_NOT_VERIFIED;
			}
			
			if ($user->isExpired()) {
				return self::RC_EXPIRED;
			}
			
			$this->setUsername($user->username);
			$this->setUserGroups($user->expandedGroups);
			$this->setUserRoles($user->expandedRoles);
			$this->setUserPermissions($user->expandedPermissions);
			$user->recordLogin();
			return self::RC_NO_ERROR;
		}
		return self::RC_USER_UNKNOWN;
	}

	public function refreshSessionACL(ZingPDO $pdo) {
		if ($user = AuthUser::findOneByUsername($pdo, $this->getUsername())) {
			$this->setUserGroups($user->expandedGroups);
			$this->setUserRoles($user->expandedRoles);
			$this->setUserPermissions($user->expandedPermissions);
		}
	}
	
	protected function setUsername($username) {
		$_SESSION['auth']['username'] = $username;
	}
	
	public function getUsername() {
		$user = $_SESSION['auth']['username'];
		return isset($user) ? $user : 'Guest';
	}
	
	protected function setUserPermissions($permissions) {
		$_SESSION['auth']['permissions'] = $permissions;
	}
	
	public function getUserPermissions() {
		return (array) $_SESSION['auth']['permissions'];
	}
	
	public function setUserGroups($groups) {
		$_SESSION['auth']['groups'] = $groups;
	}
	
	public function getUserGroups() {
		return (array) $_SESSION['auth']['groups'];
	}
	
	public function setUserRoles($roles) {
		$_SESSION['auth']['roles'] = $roles;
	}
	
	public function getUserRoles() {
		return (array) $_SESSION['auth']['roles'];
	}	

}

?>