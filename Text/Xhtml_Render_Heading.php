<?php

class Xhtml_Render_Heading extends TextParser_Renderer {

	public function render($params) {
		extract($params);
		static $nesting = array();

		$text = '';

		switch ($type) {
			case 'start':
				while (count($nesting)) {
					$nest = array_pop($nesting);
					if ($nest['level'] >= $level) {
						$text .= str_repeat("\t", $this->parser->unindent()) . "</div><!-- " . $nest['id'] . " -->\n\n";
					} else {
						$nesting[] = $nest;
						break;
					}
				}
				$nesting[] = array('level' => $level, 'id' => $id);
				$text .= str_repeat("\t", $this->parser->indent()-1) . '<div id="' . $id . '" class="' . $class . "\">\n\n" . str_repeat("\t", count($nesting)) . "<h" . $level . ">";
				break;
			case 'end':
				$text .= '</h' . $level . ">\n";
				break;
			case 'eof':
				while (count($nesting)) {
					$nest = array_pop($nesting);
					$text .= str_repeat("\t", $this->parser->unindent()) . "</div><!-- " . $nest['id'] . " -->\n\n";
				}
				break;
		}

		return $text;
	}
}

?>
