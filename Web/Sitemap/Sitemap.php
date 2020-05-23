<?php

class Sitemap extends TModule {

	public function render() {
		header('Content-Type: text/xml');

		parent::render();
	}

	public function populate($control, $params) {

		$sess = TSession::getInstance();

		foreach ($sess->app->modules as $module) {
			if ($module->hasFactory()) {
				if (isset($module->factory['class'])) {
					$class = $module->factory['class'];
					$method = $module->factory['method'];
					$factoryOutput = call_user_func_array(array($class,$method), array($sess->parameters->pdo));
					if ($factoryOutput instanceof TObjectCollection) {
						foreach ($factoryOutput as $object) {
							$control->children[] = $this->createSitemapUrl($module, $object);
						}
					} else if ($factoryOutput instanceof TObjectPersistence) {
							$control->children[] = $this->createSitemapUrl($module, $factoryOutput);
					}
				} else {
					$control->children[] = $this->createSitemapUrl($module);
				}
			}
		}

		foreach ($control->children as $child) {
			$child->doStatesUntil('preRender');
		}

	}

	private function createSitemapUrl($module, $object = null) {
/*
	<url>
		<loc>http://www.example.com/</loc>
		<lastmod>2005-01-01</lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.8</priority>
	</url>
*/

		$sess = TSession::getInstance();

		$params = array();
		if (isset($module->factory['params']) && !is_null($object)) {
			foreach($module->factory['params'] as $param => $property) {
				$encode = in_array($param, (array)$module->factory['urlencode']);
				$value = TControl::resolveBoundValue($object, $property);
				$params[$param] = $encode ? urlencode($value) : $value;
			}
		}
		$url = zing::create('THtmlControl', array('tag' => 'url'));
		$url->children[] = zing::create('THtmlControl', array('tag' => 'loc', 'innerText' => 'http://' . $sess->app->server->http_host . $module->getUri($params)));
		return $url;
	}
}

?>
