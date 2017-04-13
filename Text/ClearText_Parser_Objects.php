<?php

class ClearText_Parser_Objects extends TextParser_Parser {
	
	public $regexp = '/
						(?!\\\\)			# not preceeded by escape
						(?<=\s|^)\{			# left brace
						([^\}\s]+)			# object class
						(
							(?:
								\s+(?:\w+=[^\}\s]*?)	# params
							)*
						)
						\}(?=\s|$)			# right brace
					/smx';
					
	public function onMatch($match) {
		preg_match_all('/(\w+)="(\S*)"/m', html_entity_decode($match[2]), $m);
		$class = $match[1];
		$params = array();
		foreach ($m[0] as $index => $notused) {
			$params[$m[1][$index]] = $m[2][$index];
		}
		return $this->addToken(TextParser::SPAN, array(
									'class' => $class,
									'params' => $params));
	}

}
	
?>