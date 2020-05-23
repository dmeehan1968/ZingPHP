<?php

class AuthGroup extends TObjectPersistence {

	public	$id;
	/**
	 * @validate "Group names must contain letters, numbers and underscore characters only" regexp /^[_a-z][_a-z0-9]*$/i
	 */
	public	$name;

	public function loadRoles() {
		$sql = '	select authroles.* from authroles
					join authgroup_relatesto_authroles as agar on agar.authrole_id = authroles.id
					where agar.authgroup_id = :id
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
					join authgroup_relatesto_authperms as agap on agap.authperm_id = authperms.id
					where agap.authgroup_id = :id
					order by authperms.name';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($this->pdo, $s, 'AuthPerm');
	}

	public function loadExpandedPermissions() {
		$perms = array();
		foreach ($this->roles as $role) {
			foreach ($role->permissions as $perm) {
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

	public static function findAll(ZingPDO $pdo) {
		$sql = 'select * from authgroups order by name';
		$s = $pdo->prepare($sql);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($pdo, $s, 'AuthGroup');
	}

	public static function findById(ZingPDO $pdo, $id) {
		$sql = 'select * from authgroups where id = :id';
		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$col = new TObjectCollection($pdo, $s, 'AuthGroup');
		return $col[0];
	}

	public static function findByName(ZingPDO $pdo, $name) {
		$sql = 'select * from authgroups where name = :name';
		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $name, ZingPDO::PARAM_STR);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		$col = new TObjectCollection($pdo, $s, 'AuthGroup');
		return $col[0];
	}

	public function addRoleById($roles = array()) {
		$sql = 'insert ignore into authgroup_relatesto_authroles (authgroup_id, authrole_id)
				select :group, id from authroles where id in (' . implode(',', (array)$roles) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':group', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
	}

	public function removeRoleById($roles = array()) {
		$sql = 'delete from authgroup_relatesto_authroles where authgroup_id = :group and authrole_id in (' . implode(',',(array)$roles) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':group', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
	}

	public function addPermissionById($perms = array()) {
		$sql = 'insert ignore into authgroup_relatesto_authperms (authgroup_id, authperm_id)
				select :group, id from authperms where id in (' . implode(',', (array)$perms) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':group', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
	}

	public function removePermissionById($perms = array()) {
		$sql = 'delete from authgroup_relatesto_authperms where authgroup_id = :group and authperm_id in (' . implode(',',(array)$perms) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':group', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
	}

	public function destroy($cascade = false) {
		if ($cascade) {
			$sql = 'delete from authgroup_relatesto_authroles where authgroup_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authgroup_relatesto_authperms where authgroup_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authuser_relatesto_authgroups where authgroup_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}
		}
		parent::destroy($cascade);
	}
}


?>
