<?php

class ClearText_Parser_Links extends TextParser_Parser {
	
	public $regexp = '/
						(?<!\\\\)			# not preceeded by escape
						\[					# square bracket
						(
							(
								(?:\w[\w\d-_]*:)*	# namespace (optional)
							)
							(
								(.*?)		# uri (optional)
								(?:\#(.+?))?	# target (optional)
								(?:\?(.*?))?	# query params (optional)
							)
						)
						(?:\|(.*?))?		# supplement (optional)
						\]					# square bracket
						(?!:)				# not followed by colon (a link reference)
					/x';
					
	public function onMatch($match) {
		if (!empty($match[2])) {
			$namespace = explode(':', $match[2]);
			array_pop($namespace);	// always pop the last empty element
		} else {
			$namespace = array();
		}
		return $this->addToken(TextParser::SPAN, array(
									'namespace' => $namespace,
									'uri' => $match[4],
									'target' => $match[5],
									'query' => $match[6],
									'supplement' => $match[7],
									'linktext' => $match[3]));
	}

}
	
?>