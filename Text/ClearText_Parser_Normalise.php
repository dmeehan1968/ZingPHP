<?php

class ClearText_Parser_Normalise extends TextParser_Parser {
	
	public function parse($text, $replaced) {
		
		$replaced = 0;		// don't report replacements, or it will need a renderer
		
		$text = preg_replace(
					array(	'/\r\n?/',
							'/^\s+$/m'),						  
					array(	"\n",
							''),
					$text);
		
		return preg_replace_callback('/^.*\t.*$/m', array($this, 'onMatch'), $text);
	}
	
	public function onMatch($match) {
		$blocks = explode("\t", $match[0]);
		$line = $blocks[0];
		array_shift($blocks);
		foreach ($blocks as $block) {
			$length = strlen($line);
			$line .= str_repeat(' ', $this->parser->getTabWidth() - ($length % $this->parser->getTabWidth())) . $block;
		}
		return $line;
	}
}

?>