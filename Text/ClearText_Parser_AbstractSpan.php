<?php

/**
 * Revision History:
 *
 * 22/03/2010	Revised regexp to allow so that delim can be mid word, unless its
 * 			 	escaped (for backtoenergy.net)
 *
 */

class ClearText_Parser_AbstractSpan extends TextParser_Parser {
	
	public $delim;
	public $regexp = '/
						(?<!\\\\)				# not preceeded by escape
						(?<=\xFF|\W|^)			# must be preceeded by non-word or start
						\\@@@					# delim
						(\S.*?)					# non-space followed by any characters
						(?<=\S)					# preceeded by non-space
						(?<!\\\\)				# not preceeded by escape
						\\@@@					# delim
					/mx';
/*	public $regexp = '/
						(?<!\\\\)				# not preceeded by escape
						(?<=\xFF|\W|^)			# must be preceeded by non-word or start
						\\@@@					# delim
						(\S.*?)					# non-space followed by any characters
						(?<=\S)					# preceeded by non-space
						\\@@@					# delim
						(?=\xFF|\W|$)			# must be followed by non-word or end
					/mx';
*/	
	public function parse($text, &$replaced) {
		$this->regexp = str_replace('@@@', $this->delim, $this->regexp);
		return parent::parse($text, $replaced);
	}
	
	public function onMatch($match) {
		$start = $this->addToken(TextParser::SPAN, array('type' => 'start'));
		$end = $this->addToken(TextParser::SPAN, array('type' => 'end'));
		
		return $start . $match[1] . $end;		
	}
}


?>