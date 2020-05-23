<?php

class THtmlFileFolderView extends TCompositeControl {

	private $folder;

	public function setFolder($folder) {
		$this->folder = $folder;
	}

	public function getFolder() {
		return $this->folder;
	}

	private $recursive = false;

	public function setRecursive($bool) {
		$this->recursive = $bool;
	}

	public function getRecursive() {
		return $this->recursive;
	}

	private $linkRoot;

	public function setLinkRoot($root) {
		$this->linkRoot = $root;
	}

	public function getLinkRoot() {
		return $this->linkRoot;
	}

	public function load() {
		$sess = TSession::getInstance();
		$this->loadFolder($this, $sess->parameters['cms.files.rootpath'], $this->getFolder(), 1);
		foreach ($this->children as $child) {
			$child->doStatesUntil('preLoad');
		}
		parent::load();
	}

	public function loadFolder($container, $root, $path, $level = 1) {
		$sess = TSession::getInstance();
		if ( ! is_dir($root . '/' . $path) || ! is_readable($root . '/' . $path)) {
			return;
		}

		$folderAccess = true;

		$paths = explode('/',$path);
		$paths = array_merge(array(''), $paths);
		while (count($paths)) {

			$perm = implode('/', $paths);
			if (substr($perm, 0, 1) != '/') {
				$perm = '/' . $perm;
			}
			$perm = 'file:' . $perm;
			if ($this->authManager->hasPerm($perm)) {
				break;
			}
			array_pop($paths);
		}

		if (count($paths) < 1) {
			$folderAccess = false;
		}

		$fileCount = 0;
		$files = (array) @scandir($root . '/' . $path);
		natcasesort($files);
		$parts = explode('/', $path);
		$folder = array_pop($parts);

		if ($folderAccess) {
			if ($level > 1) {
				$container->children[] = zing::create('THtmlDiv', array('tag' => 'h' . $level, 'innerText' => $folder));
			}

			$table = zing::create('THtmlDiv', array('tag' => 'table', 'class' => 'files'));
			$table->attributes['cellspacing'] = 0;
			$th = $table->children[] = zing::create('THtmlDiv', array('tag' => 'thead'));
			$th->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'innerText' => 'Filename'));
			$th->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'innerText' => 'Modified'));
			$th->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'innerText' => 'Size'));
			$th->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'innerText' => 'Link'));
			$tbody = $table->children[] = zing::create('THtmlDiv', array('tag' => 'tr'));

			foreach ($files as $file) {
				$fileInfo = new TSPLFileInfo($root . '/' . $path . '/' . $file);

				if ($fileInfo->isDot() || $fileInfo->isDir()) {
					continue;
				}

				$fileCount++;
				$tr = $tbody->children[] = zing::create('THtmlDiv', array('tag' => 'tr', 'class' => 'file'));
				$tr->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'class' => 'filename', 'innerText' => $fileInfo->getFilename(false)));
				$tr->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'class' => 'modified', 'innerText' => $fileInfo->getMTime('jS M Y')));
				$tr->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'class' => 'size', 'innerText' => $fileInfo->getSize()));
				$linkTd = $tr->children[] = zing::create('THtmlDiv', array('tag' => 'td', 'class' => 'link'));
				$parts = explode('/', $path);
				$parts = array_slice($parts, count($parts) - $level);
				$rootpath = $this->getLinkRoot() . '/' . implode('/', $parts);
				$linkTd->children[] = zing::create('THtmlLink', array('href' => $rootpath . '/' . rawurlencode($fileInfo->getFilename()), 'innerText' => 'Download'));
			}

			if ($fileCount > 0) {
				$container->children[] = $table;
			} else if ($level > 1) {
				$container->children[] = zing::create('THtmlDiv', array('class' => 'no-files', 'innerText' => 'There are no files in this folder.'));
			}
		}

		foreach ($files as $file) {
			$fileInfo = new TSPLFileInfo($root . '/' . $path . '/' . $file);
			if (!$fileInfo->isDot() && $fileInfo->isDir() && $this->getRecursive()) {
				$this->loadFolder($container, $root, $path . '/' . $file, $level + 1);
			}
		}

		if ($level == 1 && count($container->children) < 1) {
			$container->children[] = zing::create('THtmlDiv', array('class' => 'no-files', 'innerText' => 'There are no files available.'));
		}
	}

}

?>
