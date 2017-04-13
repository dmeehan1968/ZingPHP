<?php

class ClearText_Rules extends TextParser_Rules {
	
	protected $rules = array(
		'Normalise'	=> 		10,
		
		'LinkReferences' =>	20,
		'Objects' =>		25,

		'Blockquote' =>		30,
		'Heading' =>		30,
		'List' =>			30,
		'Definition' =>		30,

		'Preformatted' =>	40,
		'Paragraph' =>		40,

		'Strong' =>			50,
		'Emphasis' =>		50,
		'Links' =>			50,

		'Entities'	=> 		100,
		'Fractions'	=> 		100,
		'LineBreak'	=> 		100,
		'Weblink'	=> 		100,
		
		);
	
	public function getClass() {
		return 'ClearText';
	}
}

?>