<?php

class ClearText_Parser_Links extends TextParser_Parser {

	public $regexp = '/
						(?<!\\\\)								# not preceeded by escape
						\[											# square bracket
						(?P<url>											# 1
							(?P<namespace>				# 2
								(?:\w[\w\d\-_]*:)?	# namespace (optional)
							)
							(?P<linktext>					# 3
								(?P<uri>.*?)				# 4 uri (optional)
								(?:\#(?P<target>.+?))?				# 5 target (optional)
								(?:\?(?P<query>.*?))?				# 6 query params (optional)
							)
						)
						(?:\|(?P<supplement>.*?))?						# 7 supplement (optional)
						\]											# square bracket
						(?!:)										# not followed by colon (a link reference)
					/x';

	public function onMatch($match) {

		if (!empty($match['namespace'])) {
			$namespace = explode(':', $match['namespace']);
			array_pop($namespace);	// always pop the last empty element
		} else {
			$namespace = array();
		}
		return $this->addToken(TextParser::SPAN, array(
									'namespace' => $namespace,
									'uri' => $match['uri'],
									'target' => $match['target'],
									'query' => $match['query'],
									'supplement' => $match['supplement'],
									'linktext' => $match['linktext']));
	}

}

?>
