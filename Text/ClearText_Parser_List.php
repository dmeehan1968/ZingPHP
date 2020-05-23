<?php

class ClearText_Parser_List extends TextParser_Parser {

	public function __construct(TextParser $parser) {
		parent::__construct($parser);
		$this->regexp = '/
						(?<=\A|\A\n|\n\n)		# preceeded by 2 newlines or start
												# dont replace existing blocks
						(?!\s*' . $this->parser->getDelim() . TextParser::BLOCK . ')
						^					# start of line
						(?:[#*]|\d+\.|[a-z]\.)		# hash or asterisk, or numbered
						[ ]+				# space after item bullet
						(?:.+?)				# capture all until
											# 2 newlines and a para which is not
											# not a list, or the end.
						(?=\n\n(?![#* ]|\d+\.|[a-z]\.)|\n*\z)
					/smx';
	}

	public function onMatch($match) {
		return
			$this->addToken(TextParser::BLOCK, array('type' => 'listStart'))
			.
			preg_replace_callback('/
						(?<=\A|\n)
						([ ]*)				# indent depth
						([#*]|\d+\.|[a-z]\.)*		# hash or asterisk, or numbered
						[ ]+
						(
							(?:
								(?![ ]*(?:(?:[#*]|\d+\.|[a-z]\.)[ ]+))		# hash or asterisk, or numbered
								(?:.+)(?:\n+|$)
							)+
						)
						/mx', array($this, 'onItemMatch'), $match[0])
			.
			$this->addToken(TextParser::BLOCK, array('type' => 'listEnd'));
	}

	public function onItemMatch($match) {

		$style = null;
		switch ($match[2][0]) {
			case '*' :
				$listType = 'ul';
				break;
			default:
				$listType = 'ol';
				if (ctype_alpha($match[2][0])) {
					$style = 'lower-alpha';
				}
		}

		$level = ((int)(strlen($match[1]) / $this->parser->getTabWidth())) + 1;

		$start = $this->addToken(TextParser::BLOCK,
								 array(
									'type' => 'itemStart',
									'level' => $level,
									'listType' => $match[2] == '*' ? 'ul' : 'ol',
									'style' => $style,
								));
		$end = $this->addToken(TextParser::BLOCK, array('type' => 'itemEnd'));

		/*
		 * Remove the indents from the lines (so paragraphs can be matched)
		 */
		$text = preg_replace('/^[ ]+(.*)/m', '\1', $match[3]);

		/*
		 * If there is more than one paragraph block, wrap the text in extra
		 * newlines so that the list item markers stand off from the item
		 * content. This will cause the list item paras to be treat as paras
		 * within the list item.
		 */
		if (preg_match_all('/^..*(?:\n\n|\n*\z)/m', $text, $m) > 1) {
			$text = "\n\n" . $text; // . "\n\n";
		}
		return $start . $text . $end;
	}
}

?>
