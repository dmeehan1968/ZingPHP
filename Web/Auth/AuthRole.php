<?php

class AuthRole extends TObjectPersistence {

	public	$id;
	/**
	 * @validate "Role names must contain letters, numbers and underscore characters only" regexp /^[_a-z][_a-z0-9]*$/i
	 */
	public	$name;

	public function loadPermissions() {
		$sql = '	select authperms.* from authperms
					join authrole_relatesto_authperms as arap on arap.authperm_id = authperms.id
					where arap.authrole_id = :id
					order by authperms.name';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
		return new TObjectCollection($this->pdo, $s, 'AuthPerm');
	}

	public static function findAll(ZingPDO $pdo) {
		$sql = 'select * from authroles order by name';

		$s = $pdo->prepare($sql);
		return self::findAllByStatement($pdo, $s, 'AuthRole');
	}

	public static function findById(ZingPDO $pdo, $id) {
		$sql = 'select * from authroles where id = :id';

		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $id, ZingPDO::PARAM_INT);

		return self::findOneByStatement($pdo, $s, 'AuthRole');
	}

	public function addPermissionById($perms = array()) {
		$sql = 'insert ignore into authrole_relatesto_authperms (authrole_id, authperm_id)
				select :role, id from authperms where id in (' . implode(',', (array)$perms) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':role', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
	}

	public function removePermissionById($perms = array()) {
		$sql = 'delete from authrole_relatesto_authperms where authrole_id = :role and authperm_id in (' . implode(',',(array)$perms) . ')';
		$s = $this->pdo->prepare($sql);
		$s->bindParam(':role', $this->id, ZingPDO::PARAM_INT);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}
	}

	public function destroy($cascade = false) {
		if ($cascade) {
			$sql = 'delete from authgroup_relatesto_authroles where authrole_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authrole_relatesto_authperms where authrole_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authuser_relatesto_authroles where authrole_id = :id';
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
