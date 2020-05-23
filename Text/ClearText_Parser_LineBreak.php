<?php

class ClearText_Parser_LineBreak extends TextParser_Parser {

	public $regexp = '/
						(?<=\A|\s)							# start or space
						(\|\|)								# double vertical bar
						(?=$|\s)							# end or space
					/x';

	public function onMatch($match) {
		return $this->addToken(TextParser::SPAN);
	}

}

?>
