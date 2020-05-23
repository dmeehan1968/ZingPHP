<?php

class ClearText_Parser_Entities extends TextParser_Parser {

	public $regexp = '/
						(
							[<>"\']				# xml escape characters
							|					# or
							&(?!(?:\w+|\#\d+|\#x[\dA-F]+);)			# ampersand that is not an HTML entity
						)
						|						# or
						(&(?:\w+|\#\d+);)					# html entities
						|						# or
						\\\\(.)					# escaped
					/x';

	public function onMatch($match) {
		if (!empty($match[1])) {
			$entity = htmlentities($match[1], ENT_QUOTES);
		} else if (!empty($match[2])) {
			$entity = $match[2];
		} else {
			$entity = $match[3];
		}
		return $this->addToken(TextParser::SPAN, array('entity' => $entity));
	}

}

?>
