<?php

class File extends TObjectPersistence {

	public	$id;
	
	public	$filename;
	/**
	 * @validate "You cannot upload a file of no size" min 1
	 */
	public 	$size;
	public	$mimetype;
	/**
	 * @validate "You must enter a short description, and it must not exceed 40 words" wordcount 1,40
	 */
	public	$short_description;
	/**
	 * @validate "The long description may not exceed 1000 words" wordcount 0,1000
	 */
	public	$long_description;
	
	public	$filetype_id;
	public	$etag = '';
	
	public	$modified;
	
	public function setContent($path) {
		$this->filePath = $path;
	}
	
	public function insert() {
		parent::insert();
		
		$this->updateData();
	}
	
	public function update() {
		parent::update();
		
		$this->updateData();
	}

	public function updateData() {

		if (isset($this->filePath)) {
			if (($file = @fopen($this->filePath,'rb')) !== false) {
				$sql = 'update files set data = :data where id = :id';
				$statement = $this->pdo->prepare($sql);
				$statement->bindParam(':data', $file, ZingPDO::PARAM_LOB);
				$statement->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
				if (! $statement->execute()) {
					throw new TObjectPdoException($statement);
				}
			} else {
				throw new Exception('unable to open file (' . $this->filePath . ')');
			}
			unset($this->filePath);
			
			$this->modified = gmdate('Y-m-d H:i:s', time());
			$sql = 'update files set etag = md5(data), modified = :modified where id = :id';
			$s = $this->pdo->prepare($sql);
			$s->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
			$s->bindParam(':modified', $this->modified, ZingPDO::PARAM_STR);
			if (! $s->execute()) {
				throw new TObjectPdoException($s);
			}
		} 
	}
	
	public function loadData() {
	
		$sql = '	select id, data from files
					where id = :id
					limit 1';

		$statement = $this->pdo->prepare($sql);
		$statement->bindParam(':id', $this->id, ZingPDO::PARAM_INT);
		$statement->bindColumn('data', $data, ZingPDO::PARAM_LOB);
		if (! $statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		$statement->fetch(ZingPDO::FETCH_BOUND);
		return $data;
	}
	
	public static function findOneByFilename(ZingPDO $pdo, $filename) {
		$tmpFile = new File($pdo);
		$sql = '	select '.$tmpFile->getColumnNamesForSql().' from files
					where filename = :filename
					limit 1';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':filename', $filename, ZingPDO::PARAM_STR);
		if (!$statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		
		$col = new TObjectCollection($pdo, $statement, 'File');
		return $col[0];
	}
	
	public static function findAllByType(ZingPDO $pdo, $filetype, $limit = 1000) {
		$tmpFile = new File($pdo);
		$sql = '	select '.$tmpFile->getColumnNamesForSql().', rand() as score from files
					where filetype_id = :filetype
					order by score
					limit :limit';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':filetype', $filetype, ZingPDO::PARAM_INT);
		$statement->bindParam(':limit', $limit, ZingPDO::PARAM_INT);
		if (!$statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		
		return new TObjectCollection($pdo, $statement, 'File');
	}
	
	public static function findOneByType(ZingPDO $pdo, $filetype) {
		$tmpFile = new File($pdo);
		$sql = '	select '.$tmpFile->getColumnNamesForSql().', rand() as score from files
					where filetype_id = :filetype
					order by score
					limit 1';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':filetype', $filetype, ZingPDO::PARAM_INT);
		if (!$statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		
		$col = new TObjectCollection($pdo, $statement, 'File');
		return $col[0];
	}
	
	public static function findOneById(ZingPDO $pdo, $id) {
		$tmpFile = new File($pdo);
		$sql = '	select '.$tmpFile->getColumnNamesForSql().' from files
					where id = :id
					limit 1';
		$statement = $pdo->prepare($sql);
		$statement->bindParam(':id', $id, ZingPDO::PARAM_INT);
		if (!$statement->execute()) {
			throw new TObjectPdoException($statement);
		}
		
		$col = new TObjectCollection($pdo, $statement, 'File');
		return $col[0];
	}
		

}

?>