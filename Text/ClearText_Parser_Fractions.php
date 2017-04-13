<?php

/**
 * Revision History:
 *
 * 22/03/2010 	Revised regexp prevent matches with slash immediately preceeding or
 * 			 	following what appears to be a fraction, as that is more likely
 * 			 	to be a date (for backtoenergy.net)
 *
 */

class ClearText_Parser_Fractions extends TextParser_Parser {
	
	public $regexp = '/
						(?<!\\\\|\/)			# not preceeded by escape or slash
						(?<=\W|^)				# must be preceeded by non-word or start
						(?P<numerator>\d+)\/(?P<denominator>\d+)
						(?=\W|$)				# must be followed by non-word or end
						(?!\/)					# not followed by slash (its a date)
					/mx';
	
	public function onMatch($match) {
		return $this->addToken(TextParser::SPAN, array('numerator' => $match['numerator'], 'denominator' => $match['denominator']));
	}
}


?>