<?php

class THtmlCmsNavigation extends THtmlDiv {

	public function load() {
		parent::load();
		$this->addClass('cms-nav');
		$this->buildMenu($this,
						 array(	'root' => $this->root,
								'sort' => $this->sort,
								'columns' => $this->columns,
								'limit' => $this->limit,
								'depth' => $this->depth,
								'module' => $this->module));
	}

	private $root;

	public function setRoot($root) {
		$sess = TSession::getInstance();

		switch ($root) {
		case '_current':
			$this->root = $sess->app->request->_uri;
			break;
		case '_parent':
			$this->root = zing::getParentPath($sess->app->request->_uri);
			break;
		default:
			$this->root = $root;
		}
	}

	public function getRoot() {
		return $this->root;
	}

	private $sort;

	public function setSort($sort) {
		$this->sort = $sort;
	}

	public function getSort() {
		return $this->sort;
	}

	private $columns;

	public function setColumns($columns) {
		$this->columns = $columns;
	}

	public function getColumns() {
		return $this->columns;
	}

	private $limit;

	public function setLimit($limit) {
		$this->limit = $limit;
	}

	public function getLimit() {
		return $this->limit;
	}

	private $depth;

	public function setDepth($depth) {
		$this->depth = $depth;
	}

	public function getDepth() {
		return $this->depth;
	}

	private $module;

	public function setModule($module) {
		$this->module = $module;
	}

	public function getModule() {
		return $this->module;
	}

	public function buildMenu($control, $params = array()) {

		$depth = isset($params['depth']) ? $params['depth'] : 0;
		$level = isset($params['level']) ? $params['level'] : 1;
		$root = (isset($params['root']) && !empty($params['root'])) ? $params['root'] : '';
		$sort = (isset($params['sort']) && !empty($params['sort'])) ? $params['sort'] : 'weight, uri';
		$columns = (isset($params['columns']) && !empty($params['columns'])) ? $params['columns'] : 'link';
		$limit = (isset($params['limit']) && !empty($params['limit'])) ? (int)$params['limit'] : 1000;
		$module = isset($params['module']) ? $params['module'] : 'Zing/Web/CMS/THtmlStandardPage';

		$columns = explode(',', $columns);

		$sess = TSession::getInstance();

		$regexp = '^' . $root . '(/[^/]*)$';
		$pages = CmsPage::findAllPublishedForNavigation($sess->parameters->pdo, $regexp, $sort, 0, $limit);

		if (count($pages)) {
			$class = array_pop(explode('/', $root));
			if (empty($class)) {
				$class = 'root';
			}

			$staticChildren = clone $control->children;
			if ($level == 1) {
				$control->children->deleteAll();
			}

			$ul = $control->children[] = zing::create('THtmlDiv', array('tag' => 'ul', 'class' => $class));
			$ul->addClass('nav-level-' . $level);

			if ($level == 1) {
				foreach ($staticChildren as $child) {
					$ul->children[] = $child;
				}
			}

			foreach ($pages as $index => $page) {

				$class = array_pop(explode('/', $page->uri));
				if (empty($class)) {
					$class = 'root';
				}
				$li = $ul->children[] = zing::create('THtmlDiv', array('tag'=>'li', 'class' => $class));
				$li->addClass('nav-level-' . $level);
				if (strcmp($sess->app->request->_uri, $page->uri) == 0) {
						$li->addClass('nav-active');
						$ul->addClass('nav-sibling-active');
				} else if (strncmp($sess->app->request->_uri,$page->uri, strlen($page->uri)) == 0) {
						$li->addClass('nav-child-active');
				} else {
						$li->addClass('nav-inactive');
				}

				foreach ($columns as $column) {
					$span = $li->children[] = zing::create('THtmlDiv', array('tag' => 'span'));
					$li->children[] = zing::create('TPlainText', array('value' => '  '));

					list($column, $args) = explode('|', $column);
					$span->setClass($column);

					switch ($column) {
						case 'link':
						case 'more':
							$span->children[] = zing::create('THtmlLink', array(
								'module' => $module,
								'uri' => $page->uri,
								'innerText' => ($column == 'more') ? $args : $page->title,
								'alt' => $page->title
								));
							break;

						case 'created':
						case 'modified':
						case 'published':
						case 'expires':
							// $args is the format string for the date
							$span->setInnerText(zing::sqlDateToNatural($page->$column, $args));
							break;
						default:
							$span->setInnerText($page->$column);
							break;
					}
				}

				if ($index == 0) $li->addClass('first');
				if ($index == count($pages)-1) $li->addClass('last');

				if ($depth == 0 || $level < $depth) {
					$params['level'] = $level+1;
					$params['root'] = $page->uri;
					$this->buildMenu($li, $params);
				}
			}

			if ($level == 1) {
				$ul->doStatesUntil('preRender');
			}
		}

	}
}

?>
