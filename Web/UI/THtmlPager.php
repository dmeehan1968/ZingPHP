<?php

class THtmlPager extends THtmlDiv {

	private $pager;

	public function __construct($params = array()) {
		$this->pager = new TPager;
		parent::__construct($params);
	}

	private $pageRequestVar = 'page';

	public function setPageRequestVar($requestVar) {
		$this->pageRequestVar = $requestVar;
	}

	public function getPageRequestVar() {
		return $this->pageRequestVar;
	}

	public function setItemCount($items) {
		$this->pager->setItemCountPerPage($items);
	}

	public function getItemCount() {
		return $this->pager->getItemCountPerPage();
	}

	public function load() {
		$this->children[] = zing::create('THtmlStyle', array('href' => '/Zing/Assets/Styles/pager.css', 'type' => 'text/css', 'rel' => 'stylesheet'));
		parent::load();
	}

	public function render() {
		$sess = TSession::getInstance();

		$this->pager->setItemArray($this->getBoundObject());
		$this->pager->setCurrentPage($sess->app->request[$this->getPageRequestVar()]);
		$this->setBoundObject($this->pager->getLimitedItemIterator());

		$div = $this->children[] = zing::create('THtmlDiv', array('class' => 'Pager'));

		$page = $this->pager->getCurrentPage();

		$div->children[] = zing::create('THtmlDiv', array('tag'=>'span', 'class' => 'pager-results',
								'innerText' => 'Showing Results ' . ($this->pager->getFirstItemKeyForCurrentPage()+1) . ' to ' . ($this->pager->getLastItemKeyForCurrentPage()+1) . ' of ' . $this->pager->getItemCount()));
		$linkTypes = array();
		if ($page > 1) {
			$linkTypes[] = 'first';
			$linkTypes[] = 'prev';
		}
		if ($this->pager->getPageCount() > 1) {
			$linkTypes[] = 'context';
		}
		if ($page < $this->pager->getPageCount()) {
			$linkTypes[] = 'next';
			$linkTypes[] = 'last';
		}
		foreach ($linkTypes as $linkType) {
			$params = array('module' => 'Zing/Web/CMS/THtmlFileList', 'dir' => $sess->app->request->dir);

			switch ($linkType) {
			case 'first':
				$params['innerText'] = 'First';
				$params['queryParams'][$this->getPageRequestVar()] = 1;
				$params['class'] = 'pager-first';
				$div->children[] = zing::create('THtmlLink', $params);
				break;
			case 'prev':
				$params['innerText'] = 'Previous';
				if ($page > 2) {
					$params['queryParams'][$this->getPageRequestVar()] = $page - 1;
				} else {
					$params['visible'] = false;
				}
				$params['class'] = 'pager-previous';
				$div->children[] = zing::create('THtmlLink', $params);
				break;
			case 'context':
				$pageMax = min($page + 5, $this->pager->getPageCount());
				$pageMin = max($page - 5, 1);
				$pageLoop = $pageMin;
				while ($pageLoop <= $pageMax) {
					if ($pageLoop == $page) {
						$div->children[] = zing::create('THtmlDiv', array('tag' => 'span', 'class' => 'pager-current ' . ($pageLoop == $pageMin ? 'pager-context-first ' : '') . ($pageLoop == $pageMax-1 ? 'pager-context-last' : ''), 'innerText' => $pageLoop));
					} else {
						$params['innerText'] = $pageLoop;
						$params['queryParams'][$this->getPageRequestVar()] = $pageLoop;
						$params['class'] = 'pager-context ' . ($pageLoop == $pageMin ? 'pager-context-first ' : '') . ($pageLoop == $pageMax-1 ? 'pager-context-last' : '');
						$div->children[] = zing::create('THtmlLink', $params);
					}
					$pageLoop++;
				}
				break;
			case 'next':
				$params['innerText'] = 'Next';
				if ($page < $this->pager->getPageCount() - 1) {
					$params['queryParams'][$this->getPageRequestVar()] = $page + 1;
				} else {
					$params['visible'] = false;
				}
				$params['class'] = 'pager-next';
				$div->children[] = zing::create('THtmlLink', $params);
				break;
			case 'last':
				$params['innerText'] = 'Last';
				$params['queryParams'][$this->getPageRequestVar()] = $this->pager->getPageCount();
				$params['class'] = 'pager-last';
				$div->children[] = zing::create('THtmlLink', $params);
				break;
			}
		}
		$div->doStatesUntil('preRender');
		parent::render();
	}
}

?>
