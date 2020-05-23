<?php

class Xhtml_Render_List extends TextParser_Renderer {

	private $lists = array();
	private $lastLevel = 0;

	public function render($params) {
		extract($params);

		$text = '';

		switch ($type) {
			case 'listStart':
				$this->lists = array();
				$this->lastLevel = 0;
				break;

			case 'itemStart':
				while ($this->lastLevel < $level) {
					$text .= str_repeat("\t", $this->parser->getIndent()) . '<' . $listType;
					if (!empty($style)) {
						$text .= ' style="list-style-type: ' . $style . '"';
					}
					$text .= ">\n";
					$this->parser->indent();
					$this->lists[$level] = $listType;
					$this->lastLevel++;
				}
				while ($this->lastLevel > $level) {
					$lastType = array_pop($this->lists);
					$text .= str_repeat("\t", $this->parser->unindent()) . '</' . $lastType . ">\n";
					$this->lastLevel--;
				}
				$text .= str_repeat("\t", $this->parser->indent()) . '<li>';
				break;

			case 'itemEnd':
				$text .= '</li>';
				$this->parser->unindent();
				break;

			case 'listEnd':
				while ($this->lastLevel > 0) {
					$lastType = array_pop($this->lists);
					$text .= str_repeat("\t", $this->parser->unindent()) . '</' . $lastType . ">\n";
					$this->lastLevel--;
				}
				break;

		}

		return $text;
	}
}

?>
