<?php

class TTemplateControl extends TCompositeControl {

	private $templatePath;

	public function preInit() {
		if ( ! $this->hasTemplatePath() ) {
			$template = $this->getDefaultTemplate();
			if (file_exists($template)) {
				$this->setTemplatePath($template);
			}
		}
		parent::preInit();
	}
	
	private $lowercaseAttribs = true;
	
	public function setLowercaseAttribs($bool) {
		$this->lowercaseAttribs = zing::evaluateAsBoolean($bool);
	}
	
	public function getLowercaseAttribs() {
		return $this->lowercaseAttribs;
	}
	
	public function convertAttrib($attrib) {
		if ($this->getLowercaseAttribs()) {
			return strtolower($attrib);
		}
		return $attrib;
	}
	
	public function init() {

		if ($this->hasTemplatePath()) {

			if (($file = file_get_contents($this->getTemplatePath())) == false) {
				throw new Exception('cannot access template file \''.$this->getTemplatePath().'\'');
			}

			preg_match_all("#<(/?)zing:([\w]+)((?:\s+\w+=\".*?\")*)\s*(/?)>#m", $file, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

			$lastOffset = 0;
			$containerStartLine = 0;
			$linenum = 1;
			$container = $this;
			
			foreach ($matches as $index => $match) {
				
				$linenum += count(explode("\n",$match[0][0])) - 1;
				
				$offset = $match[0][1];
				if ($offset > $lastOffset) {
					$html = $container->children[] = zing::create('TPlainText');
					$pcdata = substr($file, $lastOffset, $offset - $lastOffset);
					$linenum += count(explode("\n", $pcdata)) - 1;
					$html->setValue($pcdata);
					$html->preInit();
				}

				$lastOffset = $offset + strlen($match[0][0]);
				
				$isClosing = $match[1][0] == '/';
				$class = $match[2][0];
				$arguments = $match[3][0];
				$hasContent = $match[4][0] != '/';

				$off = 0;
				$objectArgs = array();

				while(preg_match("#(\w+)\s*=\s*\"(.*?)\"#i", $arguments, $property, PREG_OFFSET_CAPTURE, $off)) {
					
					$objectArgs[$this->convertAttrib(trim($property[1][0]))] = trim($property[2][0]);
					
					$off = $property[0][1] + strlen($property[0][0]);
				}

				if ($isClosing) {
					if ($class != get_class($container)) {
						throw new Exception('unexpected closing tag \''.$class.'\' on line '.$linenum . ', expected \''.get_class($container).'\' started on line '.$containerStartLine . ' in file: '.$this->getTemplatePath());
					}
					$container = $container->getContainer();
				} else {
					$object = $container->children[] = zing::create($class, $objectArgs);
					$object->preInit();
					if ($hasContent) {
						$container = $object;
						$containerStartLine = $linenum;
					}
				}

			}

			if ($this !== $container) {
				throw new Exception('unexpected end of file, tag \'' . get_class($container) . '\' started on line '.$containerStartLine . ' in file: '.$this->getTemplatePath());
			}

			if (strlen($file) > $lastOffset) {
				$html = $this->children[] = zing::create('TPlainText');
				$html->setValue(substr($file, $lastOffset, strlen($file) - $lastOffset));
				$html->preInit();
			}

		}	

		parent::init();
	}
	
	public function getTemplatePath() {
		return $this->templatePath;
	}
	
	public function setTemplatePath($path) {
		$this->templatePath = $path;
	}

	public function hasTemplatePath() {
		return isset($this->templatePath);
	}

	public function getDefaultTemplate() {	
		$rc = new ReflectionClass($this);
		return str_replace('.php', '.tpl', $rc->getFileName());
	}
}

?>