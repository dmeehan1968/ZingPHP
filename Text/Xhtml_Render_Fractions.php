<?php

class Xhtml_Render_Fractions extends TextParser_Renderer {
	public function render($params) {
		extract($params);
		return '<span class="fraction"><sup>' . $numerator . '</sup><span class="frasl">&#x2044;</span><sub>' . $denominator . '</sub></span>';
	}
}

?>