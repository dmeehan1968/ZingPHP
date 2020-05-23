<?php

class Xhtml_Render_Objects extends TextParser_Renderer {

	public function render($params) {
		$object = zing::create($params['class'], $params['params']);

		ob_start();
		$object->doStatesUntil('renderComplete');
		$text = ob_get_contents();
		ob_end_clean();
		return $text;
	}
}

?>
