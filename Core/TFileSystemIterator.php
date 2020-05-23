<?php

class TFileSystemIterator extends ArrayIterator {

	private $count = 1000;
	private $offset = 0;
	private $path;
	private $regexp;
	private $position;

	public function __construct($path, $regexp = '/.*/', $root = null, $auth = null) {
		$this->path = $path;
		$this->regexp = $regexp;
		if (! is_dir($path) || ! is_readable($path)) {
			$items = array();
		} else {
			$items = (array) @scandir($path);
		}

		if ($auth) {
			$paths = explode('/', $root);

			foreach ($paths as $index => $folder) {
				if (empty($folder)) {
					unset($paths[$index]);
				}
			}
			$paths = array_merge(array(''), $paths);

			while (count($paths)) {

				$perm = 'file:/' . implode('/', $paths);

				if ($auth->hasPerm($perm)) {
					break;
				}
				array_pop($paths);
			}

			if (count($paths) == 0) {
				throw new Exception($auth->getReason(TAuthentication::RC_NOT_AUTHORISED), TAuthentication::RC_NOT_AUTHORISED);
			}
		}

		foreach ($items as $index => $item) {
			if (!preg_match($this->regexp, $item)) {
				unset($items[$index]);
			}
		}
		natcasesort($items);
		parent::__construct($items);
	}

	public function setCount($count) {
		$this->count = $count;
	}

	public function setOffset($offset) {
		$this->offset = $offset;
		$this->rewind();
	}

	public function current() {
		return new TSPLFileInfo($this->path . parent::current());
	}

	public function valid() {
		if ($this->position < $this->count && parent::valid()) {
			return true;
		}
		return false;
	}

	public function next() {
		parent::next();
		if (parent::valid()) {
			$this->position++;
		}
	}

	public function rewind() {
		$this->position = 0;
		parent::rewind();
		$this->seek($this->offset);
	}
}

?>
