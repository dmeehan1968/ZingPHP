<?php

class ClearText_Parser_LinkReferences extends TextParser_Parser {
	
	public $regexp = '/
						(?!\\\\)			# not preceeded by escape
						^\[					# square bracket in left column
						([^\]]+?)			# link reference
						\]:[ ]*\n			# square bracket and colon
						((?:(?<=^)[ ]{4,}\w+:.+?)+)	# attribute block
						(?=\n\n|\n*\z)		# until para break or end
					/smx';
					
	public function onMatch($match) {
		preg_match_all('/(?<=^)[ ]{4,}(?P<attr>\w+):\s*(?P<value>.+?)\s*$/m', $match[2], $m);

		$attrs = array();
		foreach ($m[0] as $index => $notused) {
			$attrs[$m['attr'][$index]] = $m['value'][$index];
		}
		if (isset($attrs['href'])) {
			preg_match('/
								(?P<namespaces>(?:\w.*:)*)
								(?P<uri>.*)
								(?:\#(?P<target>.+?))?
								(?:\?(?P<query>.*?))?
						   /x', $attrs['href'], $m);
			$namespaces = explode(':', $m['namespaces']);
			array_pop($namespaces);		// last one is always empty
			$attrs['namespace'] = $namespaces;
			$attrs['uri'] = $m['uri'];
			if (isset($m['target'])) $attrs['target'] = $m['target'];
			if (isset($m['query'])) $attrs['query'] = $m['query'];
			unset($attrs['href']);
		}
		$this->parser->linkReferences[$match[1]] = $attrs;
		return '';
	}

	public function parse($text, $replaced) {
		// surpress renderer
		return parent::parse($text, $notused);
	}
}
	
?>