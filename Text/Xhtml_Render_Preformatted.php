<?php

class Xhtml_Render_Preformatted extends TextParser_Renderer {
	public function render($params) {
		extract($params);
		if ($type == 'bol') {
			return '';
		}
		$text = '';
		if ($type == 'start') {
			$text .= str_repeat("\t", $this->parser->getIndent());
		}
		return $text .= '<' . ($type == 'end' ? '/' : '') . 'pre>';
	}
}

?>