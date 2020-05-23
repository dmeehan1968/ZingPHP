<?php

class FileType extends TObjectPersistence {

	public	$id;
	public	$description;

	const ORIGINAL = 1;
	const THUMBNAIL_WEB = 2;
	const MAIN_WEB = 3;
	const BANNER_WEB = 4;
	const THUMBNAIL_PRINT = 5;

	public static function findAll(ZingPDO $pdo) {
		$sql = '	select * from filetypes order by id';
		$s = $pdo->prepare($sql);
		if (!$s->execute()) {
			throw new TObjectPdoException($s);
		}

		return new TObjectCollection($pdo, $s, 'FileType');
	}

}

?>
