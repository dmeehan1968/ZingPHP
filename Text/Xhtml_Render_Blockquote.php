<?php

class Xhtml_Render_Blockquote extends TextParser_Renderer {
	public function render($params) {
		extract($params);
		$text = '';
		if ($type == 'start') {
			$text .= str_repeat("\t", $this->parser->getIndent());
		}
		return $text .= '<' . ($type == 'end' ? '/' : '') . 'blockquote>';
	}
}

?>