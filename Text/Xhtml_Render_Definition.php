<?php

class Xhtml_Render_Definition extends TextParser_Renderer {
	
	public function render($params) {
		extract($params);
		
		$text = '';
		
		switch ($type) {
			case	'dl':
				unset($params['type']);
				$text .= str_repeat("\t", $this->parser->getIndent()) . '<' . $type;
				foreach ($params as $attr => $value) {
					$text .= ' ' . $attr . '="' . htmlentities($value) . '"';
				}
				$text .= ">\n";
				$this->parser->indent();
				break;
			case	'dt':
				$text .= "\n" . str_repeat("\t", $this->parser->getIndent()) . '<' . $type . '>';
				$this->parser->indent();
				break;
			case	'/dt':
				$text .= '<' . $type . '>';
				$this->parser->unindent();
				break;
			case	'dd':
				$text .= "\n" . str_repeat("\t", $this->parser->getIndent()) . '<' . $type . '>';
				$this->parser->indent();
				break;
			case	'/dd':
				$text .= '<' . $type . '>';
				$this->parser->unindent();
				break;
			case	'/dl':
				$text .= "\n" . str_repeat("\t", $this->parser->unindent()) . '<' . $type . '>';
				break;
		}
		
		return $text;
	}
}

?>