<?php

class ClearText_Parser_Blockquote extends TextParser_Parser {

	public function __construct(TextParser $parser) {
		parent::__construct($parser);
		$this->regexp = '/
						(?<=\A|\A\n|\n\n)		# preceeded by 2 newlines or start
												# dont replace existing blocks
						(?!\s*' . $this->parser->getDelim() . TextParser::BLOCK . ')
												# lines indented and starting with greater than
						((^\s{' . $this->parser->getTabWidth() . ',}\>\s*.*?$)+)
						(?=\n\n|\n*\z)			# followed by 2 newlines (next para not indented) or end
					/smx';
	}

	public function onMatch($match) {
		$start = $this->addToken(TextParser::BLOCK, array('type' => 'start'));
		$end = $this->addToken(TextParser::BLOCK, array('type' => 'end'));
		/*
		 * Remove the first tab width characters so that the block is not
		 * indented in the output
		 */
		$text = preg_replace('/^[ ]{' . $this->parser->getTabWidth() . '}[ ]*>[ ]*/m', '', $match[1]);
		return $start . str_replace("\n", ' ', $text) . $end;
	}
}

?>
