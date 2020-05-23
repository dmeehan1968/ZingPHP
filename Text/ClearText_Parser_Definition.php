<?php

class ClearText_Parser_Definition extends TextParser_Parser {

	public function __construct(TextParser $parser) {
		parent::__construct($parser);
		$this->regexp = '/
						(?<=\A|\A\n|\n\n)		# preceeded by 2 newlines or start
												# dont replace existing blocks
						(?!\s*' . $this->parser->getDelim() . TextParser::BLOCK . ')
						^					# start of line
						(?:[^\n]+?)			# capture a single line
						\n:[ ]+				# followed by a line starting colon
						(?:.+?)				# 2 newlines and a para which is not
											# indented, or end.
						(?=\n\n\S|\n*\z)
					/smx';
	}

	public function onMatch($match) {
		return preg_replace_callback('/
						(?<=\A|\n)
						(.+?)\n
						:[ ]*
						(
							(?:
								(?:.+)(?:\n+|$)
							)+
						)
						/mx', array($this, 'onItemMatch'), $match[0]);
	}

	public function onItemMatch($match) {
		$text .= $this->addToken(TextParser::BLOCK, array('type' => 'dl', 'id' => $this->parser->getId($match[1])));
		$text .= $this->addToken(TextParser::BLOCK, array('type' => 'dt'));
		$text .= $match[1];
		$text .= $this->addToken(TextParser::BLOCK, array('type' => '/dt'));
		$text .= $this->addToken(TextParser::BLOCK, array('type' => 'dd'));

		/*
		 * Remove the indents from the lines (so paragraphs can be matched)
		 */
		$dd = preg_replace('/^[ ]+(.*)/m', '\1', $match[2]);

		/*
		 * If there is more than one paragraph block, wrap the text in extra
		 * newlines so that the dd markers stand off from the item
		 * content. This will cause the dd paras to be treat as paras
		 * within the dd item.
		 */
		if (preg_match_all('/^..*(?:\n\n|\n*\z)/m', $dd, $m) > 1) {
			$dd = "\n\n" . $dd . "\n\n";
		}

		$text .= $dd;
		$text .= $this->addToken(TextParser::BLOCK, array('type' => '/dd'));
		$text .= $this->addToken(TextParser::BLOCK, array('type' => '/dl'));

		return $text;
	}
}

?>
