<?php

class Xhtml_Render_Weblink extends TextParser_Renderer {
	public function render($params) {
		extract($params);

		$text = '';
		
		if ($uri['scheme'] == 'mailto') {
			$text = zing::encodeEntity($uri['opaque']);
			$uri['path'] = zing::encodeHex($uri['opaque']);
		}

		$address = '';
		if ($uri['scheme']) {
			$address .= $uri['scheme'] . ':';
			
		}
		
		if ($uri['authority']) {
			$address .= '//' . $uri['authority'];
		}
		
		if ($uri['path']) {
			$address .= $uri['path'];
		}
		
		if ($uri['query']) {
			$address .= '?' . $uri['query'];
		}
		
		if ($uri['fragment']) {
			$address .= '#' . $uri['fragment'];
		}
		
		if (empty($text)) {
			$text = $address;
		}
		
		$link = '<a href="' . $address . '">' . $text . '</a>';
		return $link;
	}


}

?>