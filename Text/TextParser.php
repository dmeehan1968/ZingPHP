<?php

class TextParser {

	/*
	 * Internal storage
	 */
	private	$parsers = array();
	private $tokens = array();
	private $rulesUsed = array();
	private $renderers = array();
	private $ruleSet;
	private $ids = array();
	private $indent = 0;

	/*
	 * Configurable values
	 */
	private $delim = "\xFF";
	private $tab_width = 4;

	/*
	 * Constants
	 */

	const BLOCK = 'B';
	const SPAN = 'S';

	public function __construct($ruleSet) {
		$this->ruleSet = $ruleSet;
	}

	public function addToken($rule, $type, $params = array()) {
		static $id = 0;

		$this->tokens[$id] = array('rule' => $rule, 'params' => $params);

		return $this->delim . $type . $id++ . $this->delim;
	}

	public function addParser($parser) {
		$this->parsers[] = $parser;
	}

	public function loadParsers() {
		$this->parsers = array();
		$this->ruleSet->loadParsers($this);
	}

	public function parse($text) {
		$this->loadParsers();
		$this->rulesUsed = array();
		$this->ids = array();

		foreach ($this->parsers as $parser) {
			$replaced = 0;
			$text = $parser->parse($text, $replaced);
			if ($replaced) {
				$this->addRule($parser->rule);
			}
		}
		return $text;
	}

	public function addRule($rule) {
		$this->rulesUsed[$rule] = true;
	}

	public function addRenderer($rule, $renderer) {
		$this->renderers[$rule] = $renderer;
	}

	public function render($text, $format) {
		$this->renderers = array();
		$rules = array_keys($this->rulesUsed);
		foreach ($rules as $rule) {
			$this->ruleSet->loadRenderer($this, $format, $rule);
		}

		return preg_replace_callback('/' . $this->delim . '(\w)(\d+)' . $this->delim . '/',
								 array($this, 'onRenderToken'), $text);
	}

	public function onRenderToken($matches) {
		$token = $this->tokens[(int) $matches[2]];
		return $this->renderers[$token['rule']]->render($token['params']);
	}

	public function transform($text, $format) {
		$text = $this->parse($text);
		return $this->render($text, $format);
	}

	public function getDelim() {
		return $this->delim;
	}

	public function getToken($id) {
		return $this->tokens[$id];
	}

	public function getId($text, $unique = true) {
		$text = html_entity_decode($text);
		$text = preg_replace(array('/([^a-z0-9\-_ ])/i', 	// only keep a-z, 0-9, hyphen, underscore and space
								   '/[ ]+/',				// replace spaces with underscores
								   '/_{2,}/',				// singularise underscores
								   '/(^_+|_+$)/'),			// remove leading and trailing underscores
							 array('',
								   '_',
								   '_',
								   ''),
							 $text);
		$id = strtolower($text);
		if (is_numeric($id[0])) {
			$id = '_' . $id;
		}
		if ($unique) {
			for ($i=0 ; $i < 100 ; $i++) {
				if (isset($this->ids[$id]) && $this->ids[$id] >= $i) {
					continue;
				} else {
					$this->ids[$id] = $i;
					return $id . ($i ? '_' . $i : '');
				}
			}
		}
		return $id;
	}

	public function indent() {
		return ++$this->indent;
	}

	public function unindent() {
		if ($this->indent) {
			$this->indent--;
		}
		return $this->indent;
	}

	public function getIndent() {
		return $this->indent;
	}

	public function getTabWidth() {
		return $this->tab_width;
	}

	private $linkResolver;

	public function setLinkResolver($method) {
		$this->linkResolver = $method;
	}

	public function resolveLink($params) {
		$linkAttrs = array();

		/*
		 * Call user supplied resolver if specified
		 */
		if ($this->linkResolver) {
			$linkAttrs = call_user_func($this->linkResolver, $this, $params);
		}
		/*
		 * If resolver unspecified or failed to create link, do the default
		 * action
		 */

		if (empty($linkAttrs)) {
			extract($params);
			switch ($namespace[0]) {
				case 'Image':
					$href = '';
					if (count($namespace) > 1) {
						$href = $namespace[1] . ':';
					}
					$href .= $uri;
					$linkAttrs['image'] = $href;
					$linkAttrs['image'] .= empty($target) ? '' : '#' . $target;
					$linkAttrs['image'] .= empty($query) ? '' : '#' . $query;
					$linkAttrs['alt'] = empty($supplement) ? $linkAttrs['image'] : $supplement;
					break;
				default:

					$href = '';
					$href .= empty($namespace) ? '' : strtolower($namespace[0]) . ':';
					array_shift($namespace);
					// treat remaining namespaces like uri parts
					foreach ($namespace as &$name) {
						$name = $this->getId($name, false);
					}
					$href .= empty($namespace) ? '' : '/' . implode('/', $namespace) . '/';
					$href .= $uri;
					$href .= empty($target) ? '' : '#' . $target;
					$href .= empty($query) ? '' : '?' . $query;
					$linkAttrs['href'] = $href;
					$linkAttrs['linktext'] = empty($supplement) ? $linktext : $supplement;
					$linkAttrs['title'] = empty($title) ? $href : $title;
					if (!empty($class)) {
						$linkAttrs['class'] = $class;
					}
					if (!empty($style)) {
						$linkAttrs['style'] = $style;
					}
			}
		}

		foreach (array('image', 'alt') as $attr) {
			if (!empty($params[$attr])) {
				$linkAttrs[$attr] = $params[$attr];
			}
		}
		return $linkAttrs;
	}
}

?>
