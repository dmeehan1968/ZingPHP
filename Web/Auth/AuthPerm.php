<?php

class AuthPerm extends TObjectPersistence {

	public	$id;
	/** 
	 * @validate "Permissions names must contain letters, numbers and underscore characters only, or prefixed 'file:'" regexp /^[_a-z][_a-z0-9]*|file:.*$/i
	 */
	public	$name;
	
	public static function findAll(ZingPDO $pdo) {
		$sql = 'select * from authperms order by name';
		
		$s = $pdo->prepare($sql);
		return self::findAllByStatement($pdo, $s, 'AuthPerm');
	}
	
	public static function findById(ZingPDO $pdo, $id) {
		$sql = 'select * from authperms where id = :id';
		
		$s = $pdo->prepare($sql);
		$s->bindParam(':id', $id, ZingPDO::PARAM_INT);
		return self::findOneByStatement($pdo, $s, 'AuthPerm');
	}
	
	public function destroy($cascade = false) {
		if ($cascade) {
			$sql = 'delete from authgroup_relatesto_authperms where authperm_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authrole_relatesto_authperms where authperm_id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			if (!$s->execute()) {
				throw new TObjectPdoException($s);
			}

			$sql = 'delete from authuser_relatesto_authperms where authperm_id = :id';
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