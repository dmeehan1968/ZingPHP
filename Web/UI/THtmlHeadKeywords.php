<?php

class THtmlHeadKeywords extends THtmlHeadComponent {

	public function __construct($params = array()) {

		$this->setRenderContent(true);
		$this->setVisible(self::VIS_CHILDREN);

		parent::__construct($params);
	}

	public function updatePlaceholder($ph) {

		$content = $this->internalRender();

		if (trim($content) == '') {
			return;
		}
		$content = preg_replace('/<[\/]?.*?>/', ' ', $content);
		preg_match_all('/(?<=\s|^)[^\s,.\/\\\(){}<>\[\]\'\";:]{3,}?(?=[\s\,\.]|$)/', html_entity_decode($content), $m);
		if (count($m)) {
			$ph->setKeywords($m[0]);
		}
	}

}


?>
