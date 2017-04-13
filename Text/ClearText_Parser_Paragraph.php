<?php

class ClearText_Parser_Paragraph extends TextParser_Parser {
	
	public function __construct(TextParser $parser) {
		parent::__construct($parser);
		$this->regexp = '/
						(?<=\A|\A\n|\n\n)		# preceeded by 2 newlines or start
												# dont replace existing blocks
						(?!\s*' . $this->parser->getDelim() . TextParser::BLOCK . ')
						^(?=\S)					# must start with non-space in left margin
						(?:
												# optional class starting with period
							\.(?P<class>[a-z0-9\-\_]+)\s*
						)?
						(?P<para>.*?)			# then the para content
						(?=\n\n|\n*\z)			# followed by 2 newlines or end
					/smx';
	}
				
	public function onMatch($match) {
		$params = array('type' => 'start');
		if (!empty($match['class'])) {
			$params['class'] = $match['class'];
		}
		$start = $this->addToken(TextParser::BLOCK, $params);
		$end = $this->addToken(TextParser::BLOCK, array('type' => 'end'));
		return $start . str_replace("\n", " ", $match['para']) . $end;
	}
}

?>