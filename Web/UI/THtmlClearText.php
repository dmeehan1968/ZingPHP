<?php

class THtmlClearText extends THtmlDiv {

	public function __construct($params) {

		$this->parser = new TextParser(new ClearText_Rules);

		parent::__construct($params);
	}

	public function init() {
		parent::init();
		$this->addClass('cleartext');
	}

	public function setLinkResolver($resolver) {
		list ($class, $method) = explode(':', $resolver);
		if (empty($method)) {
			$method = $class;
			$class = NULL;
		}

		$this->parser->setLinkResolver(array($class, $method));
	}

	public function renderChildren() {

		ob_start();

		parent::renderChildren();

		$output = ob_get_contents();

		ob_end_clean();

		echo $this->parser->transform($output, 'Xhtml');

	}

	public function setInnerText($text, $forceEmpty = false) {
		return parent::setInnerText('@@' . $text . '@@', $forceEmpty);
	}

	public function getInnerText() {
		$text = parent::getInnerText();
		if (preg_match('/^@@(.*)@@$/', $text, $matches) == 1) {
			return $matches[1];
		}
		return $text;
	}

}

?>
