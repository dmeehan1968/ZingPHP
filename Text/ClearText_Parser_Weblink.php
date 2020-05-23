<?php

define('URI_ALPHA',					'[a-z]');
define('URI_DIGIT',					'[0-9]');
define('URI_ALPHANUM',				'[a-z0-9]');
define('URI_MARK',					'[\-\_\.\!\~\*\\\'\(\)]');
define('URI_RESERVED',				'[\;\/\?\:\@\&\=\+\$\,]');
define('URI_UNRESERVED',			'(?:' . URI_ALPHANUM . '|' . URI_MARK . ')');
define('URI_ESCAPED',				'(?:\%[0-9a-f]{2})');

define('URI_SCHEME',				'(?:(?P<scheme>'.URI_ALPHA.'(?:'.URI_ALPHANUM.'|\+|\-|\.)*):)');

define('URI_USERINFO',				'(?:(?P<userinfo>(?:' . URI_UNRESERVED . '|' . URI_ESCAPED . '|[\;\:\#\=\+\$\,])+)@)?');
define('URI_DOMAINLABEL',			'(?:' . URI_ALPHANUM . '(?:' . URI_ALPHANUM . '|\-)*' . URI_ALPHANUM . '\.)');
define('URI_TOPLABEL',				'(?:' . URI_ALPHA . '(?:' . URI_ALPHANUM . '|\-)*' . URI_ALPHANUM . ')');
define('URI_HOSTNAME',				'(?:(?:' . URI_DOMAINLABEL . ')*' . URI_TOPLABEL . ')');
define('URI_IPV4ADDRESS',			'(?:\d+\.\d+\.\d+\.\d+)');
define('URI_HOST',					'(?P<host>' . URI_HOSTNAME . '|' . URI_IPV4ADDRESS . ')');
define('URI_PORT',					'(?::(?P<port>\d+))?');
define('URI_HOSTPORT',				'(?:' . URI_HOST . URI_PORT . ')');
define('URI_AUTHORITY',				'(?P<authority>'. URI_USERINFO . URI_HOSTPORT . ')');

define('URI_PATH_CHAR',				'(?:' . URI_UNRESERVED . '|' . URI_ESCAPED . '|[\:\@\&\=\+\$\,])');
define('URI_PATH_SEGMENT',			'(?:' . URI_PATH_CHAR . '*(?:\;' . URI_PATH_CHAR . '*)*)');

define('URI_ABS_PATH',				'(?P<path>\/' . URI_PATH_SEGMENT . '(?:\/' . URI_PATH_SEGMENT . ')*)');
define('URI_NET_PATH',				'(?:\/\/'. URI_AUTHORITY . URI_ABS_PATH . '?)');

define('URI_URIC',					'(?:' . URI_RESERVED . '|' . URI_UNRESERVED . '|' . URI_ESCAPED . ')');
define('URI_URIC_NOSLASH',			'(?:' . URI_UNRESERVED . '|' . URI_ESCAPED . '|[\;\?\:\@\&\=\+\$\,])');
define('URI_QUERY',					'(?:\?(?P<query>' . URI_URIC . '*))');

define('URI_FRAGMENT',				'(?:\#(?P<fragment>' . URI_URIC . '*))');

define('URI_HIER_PART', 			'(?:' . URI_NET_PATH . URI_QUERY . '?)');
define('URI_OPAQUE_PART', 			'(?P<opaque>' . URI_URIC_NOSLASH . URI_URIC . '*)');
define('URI_ABSOLUTE', 				'(?:' . URI_SCHEME . '(?:' . URI_HIER_PART . '|' . URI_OPAQUE_PART . '))');

define('URI_REFERENCE',				'(?P<uri>' . URI_ABSOLUTE . URI_FRAGMENT . '?)');

// ===================================================================================================
// PLEASE NOTE: THIS IS ONLY DESIGNED TO COPE WITH ABSOLUTE AND OPAQUE URI'S WITH SCHEME SPECIFIED
// ===================================================================================================
//
// This is due to the way that the full range of URI's require multiple inclusion of the named subpatterns
// which violates regexp syntax.
//

class ClearText_Parser_Weblink extends TextParser_Parser {

	public function __construct(TextParser $parser) {
		$this->regexp = '/
					(?<=\xFF|\b|^)
					'.URI_REFERENCE.'
					(?=\xFF|\b|$)
					/xi';

			parent::__construct($parser);
	}

	public function onMatch($match) {
		foreach ($match as $key => $notused) {
			if (is_int($key)) {
				unset($match[$key]);
			}
		}

		return $this->addToken(TextParser::SPAN, array('uri' => $match));
	}

}


?>
