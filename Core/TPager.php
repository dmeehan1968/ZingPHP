<?php

class TPager implements Iterator {

	private $itemArray;

	public function __construct($itemArray = array(), $currentPage = 1) {
		$this->setItemArray($itemArray);
		$this->setItemCountPerPage(10);
		$this->setCurrentPage($currentPage);
	}

	public function setItemArray($itemArray) {
		$this->itemArray = $itemArray;
	}

	public function getItemArray() {
		return $this->itemArray;
	}

	public function setCurrentPage($page) {
		if (empty($page)) {
			$page = 1;
		}
		if ((($page - 1) * $this->getItemCountPerPage()) > $this->getItemCount()) {
			$page = (int) floor($this->getItemCount() / $this->getItemCountPerPage());
		}
		if ($page < 1) {
			$page = 1;
		}
		$this->setCurrentItemKey(($page - 1) * $this->getItemCountPerPage());
	}

	public function getCurrentPage() {
		return (int) floor($this->getCurrentItemKey() / $this->getItemCountPerPage()) + 1;
	}

	private $itemCountPerPage;

	public function setItemCountPerPage($items) {
		$this->itemCountPerPage = $items;
	}

	public function getItemCountPerPage() {
		return $this->itemCountPerPage;
	}

	private $itemKey;

	public function getCurrentItemKey() {
		return $this->itemKey;
	}

	public function setCurrentItemKey($itemKey) {
		if ($itemKey > $this->getItemCount()) {
			$itemKey = $this->getItemCount();
		}
		$this->itemKey = $itemKey;
	}

	public function getFirstItemKeyForCurrentPage() {
		return $this->itemKey;
	}

	public function getLastItemKeyForCurrentPage() {
		return min($this->itemKey + ($this->getItemCountPerPage()-1), $this->getItemCount()-1);
	}

	public function getItemCount() {
		return count($this->itemArray);
	}

	public function getPageCount() {
		return (int) ceil($this->getItemCount() / $this->getItemCountPerPage());
	}

	public function getLimitedItemIterator() {
		return new LimitIterator($this->getItemArray(), $this->getFirstItemKeyForCurrentPage(), $this->getItemCountPerPage());
	}

	public function rewind() {
		reset($this->itemArray);
	}

	public function next() {
		return next($this->itemArray);
	}

	public function current() {
		return current($this->itemArray);
	}

	public function key() {
		return key($this->itemArray);
	}

	public function valid() {
		return current($this->itemArray) !== false;
	}

	public function __call($name, $arguments = array()) {
		return call_user_func_array(array($this->itemArray, $name), $arguments);
	}
}

?>
