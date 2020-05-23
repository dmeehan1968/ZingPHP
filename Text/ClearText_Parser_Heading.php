<?php

class ClearText_Parser_Heading extends TextParser_Parser {

	public $regexp = '/
						(?<=\A|\A\n|\n\n)		# preceeded by 2 newlines or start
						^						# start of line
						(?!\\\\)				# not preceeded by escape
						(?:
							([\-=+]{1,6})		# 1-6 hypen, equals or plus
							\s*(.+?)\s*			# strip space but capture one or more
							\1					# repeat of opening sequence
							\s*					# strip trailing spaces
							|					# or
							\s*
								(?:\.(\w[\w\d\-_]*)\s+)?	# .classname
								(.+?)\s*\n		# text on a line
							((?:[=\-]){5,})		# followed by 5 or more equals or hyphens
						)
						(?=\n\n|\n*\z)		# followed by 2 newlines or end
					/mx';

	public function parse($text, &$replaced) {
		$text = parent::parse($text, $replaced);
		if ($replaced) {
			/*
			 * Add an EOF marker so that we can clean up any nesting
			 */
			$text .= "\n\n" . $this->addToken(TextParser::BLOCK, array('type' => 'eof'));
		}
		return $text;
	}

	public function onMatch($match) {
		if (!empty($match[1])) {
			$heading = $match[2];
			$id = $this->parser->getId($heading);
			$level = strlen($match[1]);
		} else {
			$heading = $match[4];
			$id = $this->parser->getId($heading);
			$level = $match[5][0] == '=' ? 1 : 2;
		}
		$class = $this->parser->getId($heading, false);
		if (!empty($match[3])) {
			$class .= (!empty($class) ? ' ' : '') . $match[3];
		}

		$params = array('id' => $id, 'level' => $level, 'class' => $class);
		$start = $this->addToken(TextParser::BLOCK, array_merge($params, array('type' => 'start')));
		$end = $this->addToken(TextParser::BLOCK, array_merge($params,array('type' => 'end')));

		return $start . $heading . $end;
	}
}

?>
