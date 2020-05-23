<?php

class ClearText_Parser_Preformatted extends TextParser_Parser {

	public function __construct(TextParser $parser) {
		parent::__construct($parser);
		$this->regexp = '/
						(?<=\A|\A\n|\n\n)		# preceeded by 2 newlines or start
												# dont replace existing blocks
						(?!\s*' . $this->parser->getDelim() . TextParser::BLOCK . ')
												# lines indented by tabwidth or more
						((?:^[ ]{' . $this->parser->getTabWidth() . ',}.*?$)+)
						(?=\n\n\S|\n*\z)		# followed by 2 newlines (next para not indented) or end
					/smx';
	}

	public function onMatch($match) {
		$start = $this->addToken(TextParser::BLOCK, array('type' => 'start'));
		$end = $this->addToken(TextParser::BLOCK, array('type' => 'end'));
		/*
		 * Remove the first tab width characters so that the block is not
		 * indented in the output.  It's necessary to insert a 'beginning of
		 * line' (BOL) where the left margin is so that the
		 * preformatted line breaks don't get confused for other block
		 * elements.  These are removed by the renderer.
		 */
		$text = preg_replace('/^[ ]{' . $this->parser->getTabWidth() . '}/m',
						$this->addToken(TextParser::BLOCK, array('type' => 'bol')), $match[1]);

		/*
		 * Escape ClearText markup (i.e. markup not supported in preformatted)
		 */
		$text = preg_replace('/(?<!\\\\)\[/', '\\[', $text);

		return $start . $text . $end;
	}
}


?>
