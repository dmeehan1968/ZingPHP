<?php

class Xhtml_Render_Links extends TextParser_Renderer {
	
	public function render($params) {
		
		$linkRefs = $this->parser->linkReferences[$params['linktext']];

		if ($linkRefs) {
			// always override uri if specified
			if (!empty($linkRefs['uri'])) {
				$params['uri'] = $linkRefs['uri'];
			}
			foreach (array('namespace', 'target', 'query', 'title', 'alt', 'caption', 'image', 'class', 'style') as $attr) {
				if (empty($params[$attr]) && !empty($linkRefs[$attr])) {
					$params[$attr] = $linkRefs[$attr];
				}
			}
		}
		$linkAttrs = $this->parser->resolveLink($params);

		if (isset($linkRefs['link']) && ! zing::evaluateAsBoolean($linkRefs['link'])) {
			unset($linkAttrs['href']);
		}

		if ($linkRefs['rel'] && !empty($linkRefs['rel'])) {
			$linkAttrs['rel'] = $linkRefs['rel'];
		}
		
		$linkText = $linkAttrs['linktext'];
		unset($linkAttrs['linktext']);

		if ($linkAttrs['image']) {
			$image['src'] = $linkAttrs['image'];
			if (!empty($linkAttrs['class'])) {
				$image['class'] = $linkAttrs['class'];
			}
			unset($linkAttrs['image']);
		}
		if ($linkAttrs['alt']) {
			$image['alt'] = $linkAttrs['alt'];
			$image['title'] = empty($linkAttrs['title']) ? $image['alt'] : $linkAttrs['title'];
			unset($linkAttrs['alt']);
		}
		if ($params['caption']) {
			$image['caption'] = $params['caption'];
		}
		
		if (empty($linkAttrs['class'])) {
			unset($linkAttrs['class']);
		}

		$text = '';
		
//		if (isset($image['src'])) {
//			$text .= '<div class="image' . (!empty($image['class']) ? ' ' . $image['class'] : '') . '">';
//		}
		
		/*
		 * Only create a link if the href is set
		 */
		if (isset($linkAttrs['href'])) {
			$text .= '<a';
			foreach ($linkAttrs as $attr => $value) {
				$text .= ' ' . $attr . '="' . htmlentities($value) .'"';
			}
			$text .= '>';
			
		}
		
		unset($linkAttrs['style']);		// doesn't apply to image

		if (isset($image['src'])) {
			// insert image
			$shadow = zing::evaluateAsBoolean($linkRefs['shadow']);
			$text .= ($shadow ? '<span class="drop-shadow"><span class="drop-shadow-1"></span><span class="drop-shadow-2"></span>' : '') . '<img';
			foreach ($image as $attr => $value) {
				$text .= ' ' . $attr . '="' . htmlentities($value) . '"';
			}
			$text .= ' />';
			if (!empty($image['caption'])) {
				$text .= '<span class="caption">' . htmlentities($image['caption']) . '</p>';
			}
			$text .= ($shadow ? '</span>' : '');
		} else {
			$text .= $linkText;
		}
		
		if (isset($linkAttrs['href'])) {
			$text .= '</a>';
		}

//		if (isset($image['src'])) {
//			$text .= '</div>';
//		}
		
		return $text;
	}
	
	public function makeUrl($namespace, $uri, $target, $query) {
		$url = '';
		$url .= empty($namespace) ? '' : implode((array)$namespace, ':') . ':';
		$url .= empty($uri) ? '' : $uri;
		$url .= empty($target) ? '' : '#' . $target;
		$url .= empty($query) ? '' : '?' . $query;
		return htmlentities($url);
	}
}

?>