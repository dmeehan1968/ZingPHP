<?php

class Xhtml_Render_Paragraph extends TextParser_Renderer {
	public function render($params) {
		extract($params);
		$text = '';
		if ($type == 'start') {
			$text .= str_repeat("\t", $this->parser->getIndent());
		}
		return $text . '<' . ($type == 'end' ? '/' : '') . 'p' . (!empty($class) ? ' class="' . $class . '"' : '') . '>';
	}
}

?>
