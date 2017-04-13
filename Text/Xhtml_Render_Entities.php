<?php

class Xhtml_Render_Entities extends TextParser_Renderer {
	public function render($params) {
		extract($params);
		return $entity;
	}
}

?>