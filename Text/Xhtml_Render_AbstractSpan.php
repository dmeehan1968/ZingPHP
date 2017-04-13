<?php

class Xhtml_Render_AbstractSpan extends TextParser_Renderer {
	
	public function render($params) {
		extract($params);
		$parts = explode('_', get_class($this));
		$tag = strtolower(array_pop($parts));
		return '<' . ($type == 'end' ? '/' : '') . $tag . '>';
	}
}

?>