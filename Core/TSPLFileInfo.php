<?php

class TSPLFileInfo extends SPLFileInfo {
	
	public function __get($name) {
		$name = 'get' . $name;
		if (method_exists($this, $name)) {
			return $this->$name();
		}
	}
	
	public function isDot() {
		$filename = parent::getFilename();
		return $filename[0] == '.';
	}
	
	public function getSize() {
		if ($this->isDir()) {
			return '-';
		}
		$scale = array('bytes', 'KB', 'MB', 'GB', 'TB');
		$size = parent::getSize();
		while ($size > 1024) {
			$size /= 1024;
			array_shift($scale);
		}
		return ((int)$size) . ' ' . array_shift($scale);
	}
	
	public function getExtension() {
		preg_match_all('/(?:.*\.(?P<ext>.*))|(.*)$/', parent::getFilename(), $m);
		return $m['ext'][0];
	}
	
	public function getFilename($withExt = true) {
		$file = parent::getFilename();
		if ($file == '..') {
			return '<parent>';
		}
		
		if ($withExt) {
			return $file;
		}
		
		$parts = explode('.', $file);
		if (count($parts) > 1) {
			array_pop($parts);
		}
		return implode('.', $parts);
	}

	public function getMTime($format = null) {
		if (is_null($format)) {
			return parent::getMTime();
		}
		return gmdate($format, parent::getMTime());
	}
	
	public function getATime($format = null) {
		if (is_null($format)) {
			return parent::getATime();
		}
		return gmdate($format, parent::getATime());
	}
	
	public function getCTime($format = null) {
		if (is_null($format)) {
			return parent::getCTime();
		}
		return gmdate($format, parent::getCTime());
	}
	
	public function getIconPath() {	
		$icons = array(
						'jpg' => 'Imagen-JPG-32x32.png',
						'png' => 'Imagen-PNG-32x32.png',
						'gif' => 'Imagen-GIF-32x32.png',
						'bmp' => 'Imagen-BMP-32x32.png',
						'doc' => 'Oficina-DOC-32x32.png',
						'pdf' => 'Oficina-PDF-32x32.png',
						'ppt' => 'Oficina-PPT-32x32.png',
						'xls' => 'Oficina-XLS-32x32.png',
						'txt' => 'Oficina-TXT-32x32.png',
						'zip' => 'Comprimidos-ZIP-32x32.png',
					    'generic' => 'Sistema-Default-32x32.png',
						'dir' => 'Lightbrown-Generic-32x32.png'
						);
		
		$ext = $this->getExtension();
		if (empty($ext) || ! array_key_exists($ext, $icons)) {
			if ($this->isDir()) {
				$ext = 'dir';
			} else {
				$ext = 'generic';
			}
		}
		return '/Zing/Assets/Images/icons/' . $icons[$ext];
	}

	public function getPerms() {
		$code = parent::getPerms();
		$info = '----------';
		$perms = array( 0xC000 => array('offset' => 0, 'char' => 's'),
					    0xA000 => array('offset' => 0, 'char' => 'l'),
						0x8000 => array('offset' => 0, 'char' => '-'),
						0x6000 => array('offset' => 0, 'char' => 'b'),
						0x4000 => array('offset' => 0, 'char' => 'd'),
						0x2000 => array('offset' => 0, 'char' => 'c'),
						0x1000 => array('offset' => 0, 'char' => 'p'),
						0x0100 => array('offset' => 1, 'char' => 'r'),
						0x0080 => array('offset' => 2, 'char' => 'w'),
						0x0040 => array('offset' => 3, 'char' => 'x'),
						0x0020 => array('offset' => 4, 'char' => 'r'),
						0x0010 => array('offset' => 5, 'char' => 'w'),
						0x0008 => array('offset' => 6, 'char' => 'x'),
						0x0004 => array('offset' => 7, 'char' => 'r'),
						0x0002 => array('offset' => 8, 'char' => 'w'),
						0x0001 => array('offset' => 9, 'char' => 'x')
					  );
		
		foreach ($perms as $bits => $a) {
			if (($code & $bits) == $bits) {
				$info[$a['offset']] = $a['char'];
			}
		}
		return $info;
	}
	
	public function getType() {
		$type = parent::getType();
		switch ($type) {
		case 'dir':
			return 'Folder';
		case 'file':
			return strtoupper($this->getExtension()) . ' File';
		default:
			return 'Unknown';
		}
	}
		
}

?>