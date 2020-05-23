<?php

class THtmlZingDiv extends TRawOutput {
	
	public function render() {
		$output = $this->getInnerText();
		
		preg_match_all('/<!--\s*(.*)\s*-->/',$output, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		$offset = 0;
		foreach ($matches as $m) {
			if ($m[0][1] > $offset) {
				$temp = substr($output, $offset, $m[0][1] - $offset);
				echo $temp;
			}
			
			if (preg_match('/(?:^|<!--\s*)object:(?P<class>[\w\d_]+)(?:\((?P<args>[^\)]*)\))?\s*(?:$|\s*-->)/i', $m[1][0], $code)) {
				$class = $code['class'];
				$argArray = array();

				if (isset($code['args'])) {
					preg_match_all('/(?:(?P<arg>\w+)=(?:(?P<value>\"[^\"]*\"|[^,]+)))*/',$code['args'],$args);
					
					foreach ($args[0] as $index => $notused) {
						if (!empty($args['arg'][$index])) {
							$value = $args['value'][$index];
							if ($value[0] == '"') {
								$value = substr($value, 1, strlen($value) - 2);
							}
							$argArray[trim($args['arg'][$index])] =	$value;
						}
					}
				}
				$object = zing::create($class, $argArray);
				$object->doStatesUntil('renderComplete');
			}
			
			$offset = $m[0][1] + strlen($m[0][0]);
		}

		if ($offset < strlen($output)) {
			$temp = substr($output, $offset);
			echo $temp;
		}
	}
}

class THtmlStandardPage extends TModule {

	public function preInit() {
		parent::preInit();
		
		$sess = TSession::getInstance();
		$timeout = $sess->parameters['cms.standardpage.cache.timeout'];
		if (empty($timeout)) {
			$timeout = -1;
		}
		$sess->app->page->setCacheTimeout($timeout);
	}

	public function load() {
	
		$sess = TSession::getInstance();
		
		$page = CmsPage::FindOnePublishedByUri($sess->parameters->pdo, $sess->app->request->uri);

		$this->setBoundObject($page);
		
		parent::load();
	}
	
	public function preRender() {
		$page = $this->getBoundObject();
		if ($page->body != strip_tags($page->body)) {
			$ctl = $this->body->children[] = zing::create('THtmlZingDiv', array('boundProperty' => $this->body->getBoundProperty()));
		} else {
			$format = $this->session->parameters['cms.standardpage.format'];
			$ctl = $this->body->children[] = zing::create((empty($format) ? 'THtmlFormattedDiv' : $format), array('boundProperty' => $this->body->getBoundProperty()));
		}
		
		$ctl->doStatesUntil('postComplete');
		parent::preRender();
	}
	
	public function render() {
	
		if (is_null($this->getBoundObject())) {
			$this->children->deleteAll();
			
			$content = $this->children[] = zing::create('StaticOrNotFound');
			$content->doStatesUntil('render');
		} else {
			parent::render();
		}
	}
	
	public function setBodyId($control, $params) {
		$page = $this->getBoundObject();
		if (!empty($page)) {
			$control->setId(zing::urltext($page->title));
			
			$classes = preg_split('/\//', $page->uri);
			foreach ($classes as $class) {
				if (strlen($class)) {
					$control->addClass($class);
				}
			}			
		}
	}
	
	public function setEditLink($control, $params) {
		$page = $this->getBoundObject();
		if (!empty($page)) {
			$control->setPage_Id($page->id);
		}
	}

	public function insertPageTitle($control, $params) {
		$page = $this->getBoundObject();
		$control->setTitle($page->title);
	}
}


?>
