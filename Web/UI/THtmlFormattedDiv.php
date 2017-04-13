<?php

class THtmlFormattedDiv extends THtmlDiv {

	private $paragraph = true;
	
	public function setParagraph($bool) {
		$this->paragraph = zing::evaluateAsBoolean($bool);
	}
	
	public function getParagraph() {
		return $this->paragraph;
	}

	public function setInnerText($text) {
		$this->children->deleteAll();
		parent::setInnerText($text);	
	}
	
	public function preRender() {
		ob_start();
		parent::preRender();
		parent::renderChildren();
		$output = html_entity_decode(ob_get_contents());
		ob_end_clean();

		$this->addClass('formatted-div');

		$this->children->deleteAll();
		
		// strip whitespace from line ends, and then split on 1 or more newlines.  Empty lines are ignored.
		
		$paras = preg_split('/(?:\s*\n){2,}/', $output, -1, PREG_SPLIT_NO_EMPTY);
		
		$top = $container = $this->children[] = zing::create('TCompositeControl');
		
		$formattingEnabled = true;
		
		foreach ($paras as $para) {

			/**
			 *
			 * == DEFINITION LISTS
			 *
			 * Term :: Definition
			 *
			 * Consequetive definitions are placed within the same list. Any other para type between
			 * definitions will split the list.
			 */
			 
			if (preg_match('/^(?P<term>[\w\d\s\-\.]+)\s::\s(?P<def>.*)/', $para, $m)) {
				if (get_class($container) == 'TCompositeControl' ||
						($container instanceof THtmlControl && $container->getTag() != 'dl')) {
					// insert the DL if we are at the top level
					$container = $container->children[] = zing::create('THtmlDiv', array('tag' => 'dl'));
				}

				$def = $container->children[] = zing::create('THtmlDefinition');
				$def->getTermControl()->children[] = zing::create('THtmlFormattedDiv', array('paragraph' => false, 'innerText' => $m['term'], 'visible' => self::VIS_CHILDREN));
				$def->getDefinitionControl()->children[] = zing::create('THtmlFormattedDiv', array('paragraph' => false, 'innerText' => $m['def'], 'visible' => self::VIS_CHILDREN));
				continue;
			}

			if ($container instanceof THtmlControl && $container->getTag() == 'dl') {
				$container = $container->getContainer();
			}
			
			/**
			 * DIV
			 *
			 * [#id.class.class]{perms="value"}
			 *
			 * yada, yada, yada
			 *
			 * [/class]
			 *
			 * id and class are optional, id must appear first.  multiple classes
			 * can be specified with dot or space delimiters.
			 * Permissions are optional, but allow content blocks to be excluded from view by unauthorised
			 * users.  'authGuest', 'authPerms', 'authRoles' and 'authGroups' are permitted 'perms' values.
			 *
			 * Script - if the class is 'script', the enclosed content is interpretted as javascript
			 */
			 
			if (preg_match('/^\[(?P<close>\/?)(?:\#(?P<id>[\w][\w0-9\-_]*))?(?:\.?(?P<class>[\w0-9\-_\s\.]+))?\](?:{(?P<perms>.*)})?\s*$/', $para, $m)) {
				if ($m['close'] != '/') {
					// OPEN TAG
					$params = array();
					if (!empty($m['id'])) {
						$params['id'] = $m['id'];
					}
					if (!empty($m['class'])) {
						$params['class'] = implode(' ',explode('.',$m['class']));
					}
					if (!empty($m['perms'])) {
						preg_match_all('/(?P<attr>\w+)="(?P<value>[^"]*)"/', $m['perms'], $pairs);
						foreach ($pairs['attr'] as $index => $attr) {
							if (in_array($attr, array('authGuest', 'authPerms', 'authRoles', 'authGroups'))) {
								$params[$attr] = $pairs['value'][$index];
							}
						}
					}
					if ($params['class'] == 'script') {
						unset($params['class']);
						$params['type'] = 'text/javascript';
						$tag = 'THtmlInlineScript';
						$formattingEnabled = false;
					} else {
						$tag = 'THtmlDiv';
					}
					$container = $container->children[] = zing::create($tag, $params);
				} else {
					// CLOSE TAG
					if ($m['class'] == 'script') {
						$formattingEnabled = true;
					}
					$container = $container->getContainer();
				}

				continue;
			}
		
			if (! $formattingEnabled) {
				$paraCtl = $container->children[] = zing::create('TRawOutput');
				$paraCtl->setInnerText($para);
				continue;
			}
			
			/**
			 *
			 * OBJECTS
			 *
			 * object:class(arg=value,...)
			 *
			 */
			if (preg_match('/^object:(?P<class>[\w\d_]+)(?:\((?P<args>.*)\))?\s*$/i', $para, $m)) {
				$class = $m['class'];
				$argArray = array();

				if (isset($m['args'])) {
					preg_match_all('/(?:(?P<arg>\w+)=(?:(?P<value>\"[^\"]*\"|[^,]+)))*/',$m['args'],$args);
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
				$object = $container->children[] = zing::create($class, $argArray);
				continue;
			}
	
		
			/** 
			 * HEADINGS
			 *
			 * h1. Heading Text
			 *
			 * Must start at beginning of line.  Digit can be 1-6.
			 */
								
			if (preg_match('/^h(?P<level>[1-6])\.(?P<class>[\w0-9\-_]*)?\s(?P<title>.*)/', $para, $m)) {
				$params = array('tag' => 'h'.$m['level'], 'paragraph' => false, 'innerText' => trim($m['title']));
				if (isset($m['class'])) {
					$params['class'] = $m['class'];
				}
				$container->children[] = zing::create('THtmlFormattedDiv', $params);
				$container->children[] = zing::create('TPlainText', array('value' => "\r\n"));
				continue;
			}

			/** 
			 * Paragraphs with id and/or class
			 *
			 * #id.classname.classname... yada, yada, yada...
			 *
			 * Period or hash must start at beginning of line.  The line can be empty, 
			 * in which case the para will still render.  Useful for
			 * inserting clearing divs for floated objects.
			 * id must come first, followed by zero or more classnames preceeded with dot.
			 */
								
			if (preg_match('/^(?:#(?P<id>[\w][\w0-9\-_]*))?(?:\.(?P<class>[\w][\w0-9\-_\.]+))?\s+(?P<para>.*)/', $para, $m)) {
				if (!empty($m['id']) || !empty($m['class'])) {
					$params = array('tag' => 'p', 'hidewhenempty' => false, 'paragraph' => false, 'innerText' => trim($m['para']));
					if(!empty($m['id'])) {
						$params['id'] = $m['id'];
					}
					if(!empty($m['class'])) {
						$params['class'] = implode(' ',explode('.',$m['class']));
					}
					$container->children[] = zing::create('THtmlFormattedDiv', $params);
					$container->children[] = zing::create('TPlainText', array('value' => "\r\n"));
					continue;
				}
			}

			/**
			 * == LISTS - ORDERED AND UNORDERED
			 *
			 * * Bulleted Item 1
			 * ** Bulleted Item 1.1
			 *
			 * # Numbered Item 1
			 * ## Numbered Item 1.1
			 *
			 */ 
						
			if (preg_match_all('/^(?P<style>\*+|#+)\s+(?P<item>.*)(?:\n|$)/m', $para, $m)) {					
				$style = ($m['style'][0] == '*') ? 'ul' : 'ol';
				$depth = 0;
				$list = '';
				$listCtl = $container;
				foreach ($m['item'] as $index => $item) {
					while ($depth < strlen($m['style'][$index])) {
						$listCtl = $listCtl->children[] = zing::create('THtmlDiv', array('tag' => $style));
						$listCtl->getContainer()->children[] = zing::create('TPlainText', array('value' => "\r\n"));
						$listCtl->children[] = zing::create('TPlainText', array('value' => "\r\n"));
						$depth++;
					}
					while ($depth > strlen($m['style'][$index])) {
						$listCtl = $listCtl->getContainer();
						$depth--;
					}
					$li = $listCtl->children[] = zing::create('THtmlFormattedDiv', array('tag' => 'li', 'paragraph' => false, 'innerText' => trim($item)));
					if ($index == 0) $li->addClass('first');
					if ($index == count($m['item'])-1) $li->addClass('last');
					$listCtl->children[] = zing::create('TPlainText', array('value' => "\r\n"));
				}
				continue;
			}
		
			/**
			 * == TABLES
			 *
			 * |Col 1|Col 2|Col 3|
			 * |Col 1|Col 2|Col 3|
			 *
			 */ 
						
			if (preg_match_all('/^(?P<row>\|.*\|)/m', $para, $m)) {					
				$tbl = $container->children[] = zing::create('THtmlControl', array('tag' => 'table'));
				$tbl->attributes['cellspacing'] = 0;
				$tblBody = $tbl->children[] = zing::create('THtmlDiv', array('tag' => 'tbody'));
				foreach ($m['row'] as $rowIndex => $row) {
					$tblRow = $tblBody->children[] = zing::create('THtmlTableRow');
					if ($rowIndex == 0) {
						$tblRow->addClass('first');
					}
					if ($rowIndex == count($m['row'])-1) {
						$tblRow->addClass('last');
					}
					$tblRow->addClass($rowIndex % 2 ? 'even' : 'odd');
					$columns = explode('|', $row);
					array_shift($columns);
					array_pop($columns);
					foreach ($columns as $colIndex => $column) {
						$td = $tblRow->children[] = zing::create('THtmlTableData');
						if ($colIndex == 0) {
							$td->addClass('first');
						}
						if ($colIndex == count($columns)-1) {
							$td->addClass('last');
						}
						$td->addClass($colIndex % 2 ? 'even' : 'odd');
						$td->children[] = zing::create('THtmlFormattedDiv', array('innerText' => $column, 'paragraph' => false));
					}
				}
				continue;
			}
		
			/** 
			 * == REMAINDER IS INLINE TEXT, SO DETERMINE IF PARA IS NEEDED
			 *
			 */
			
			if ($this->getParagraph()) {
				$paraCtl = $container->children[] = zing::create('THtmlDiv', array('tag' => 'p'));
				$container->children[] = zing::create('TPlainText', array('value' => "\r\n"));
			} else {
				$paraCtl = $container->children[] = zing::create('THtmlDiv', array('visible' => self::VIS_CHILDREN));;
			}

			/**
			 * == BOLD AND ITALIC
			 *
			 * *text* is bold
			 * _text_ is italic
			 *
			 * To prevent inline underscore or asterisk, such as in email addresses
			 * or mathematical expressions from being treated as start of expression
			 * the sequence must be proceeded and followed by whitespace or
			 * punctuation.
			 *
			 */
			
			if (preg_match_all("/(?<=^|\s|\pP)(?P<style>\*|_)(?P<text>.+?)(?:\\1)(?=\s|$|\pP)/m",$para, $m, PREG_OFFSET_CAPTURE)) {
				$offset = 0;
				foreach ($m[0] as $index => $source) {
					if ($source[1] > $offset) {
						$text = substr($para, $offset, $source[1]-$offset);
						$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
					}
					$offset = $source[1] + strlen($source[0]);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('tag' => $m['style'][$index][0] == '*' ? 'strong' : 'em', 'paragraph' => false, 'innerText' => $m['text'][$index][0]));
				}
				if ($offset < strlen($para)) {
					$text = substr($para, $offset);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
				}
				continue;
			}		

			/**
			 * == HTML Entities
			 *
			 * &name;
			 *
			 */
			
			if (preg_match_all("/\&[a-z]+?\;/m",$para, $m, PREG_OFFSET_CAPTURE)) {
				$offset = 0;
				foreach ($m[0] as $index => $source) {
					if ($source[1] > $offset) {
						$text = substr($para, $offset, $source[1]-$offset);
						$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
					}
					$offset = $source[1] + strlen($source[0]);
					$paraCtl->children[] = zing::create('TPlainText', array('value' => $m[0][$index][0]));
				}
				if ($offset < strlen($para)) {
					$text = substr($para, $offset);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
				}
				continue;
			}		

            /**
             * == LINE BREAKS
             *
             * first line || second line
             */
            
			if (preg_match_all("/\s+\|\|\s+/m",$para, $m, PREG_OFFSET_CAPTURE)) {
				$offset = 0;
				foreach ($m[0] as $index => $source) {
					if ($source[1] > $offset) {
						$text = substr($para, $offset, $source[1]-$offset);
						$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
					}
					$offset = $source[1] + strlen($source[0]);
					$paraCtl->children[] = zing::create('THtmlBr');
				}
				if ($offset < strlen($para)) {
					$text = substr($para, $offset);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
				}
				continue;
			}		

            /**
             * == BOOKMARKS
             *
             * @bookmark@
             */
            
			if (preg_match_all("/@(?P<bookmark>[^\|@]+)(\|(?P<inner>[^@]*))?@/",$para, $m, PREG_OFFSET_CAPTURE)) {
				$offset = 0;
				foreach ($m[0] as $index => $source) {
					if ($source[1] > $offset) {
						$text = substr($para, $offset, $source[1]-$offset);
						$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
					}
					$offset = $source[1] + strlen($source[0]);
					$paraCtl->children[] = zing::create('THtmlDiv', array('tag' => 'a', 'name' => $m['bookmark'][$index][0], 'innerText' => $m['inner'][$index][0], 'hideWhenEmpty' => false, 'collapse' => false));
				}
				if ($offset < strlen($para)) {
					$text = substr($para, $offset);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
				}
				continue;
			}		

			/**
			 * == INLINE IMAGES
			 *
			 * !http://domain.com/image.jpg|class=classname|style=csscode|alt=alttext|link=http!
			 *
			 * class, style and alt must be specified in that order, but are all optional
			 *
			 */
			 
			if (preg_match_all('/!(?P<url>(?:[A-Z]+:\/\/)?[^\.]+\.(?:jpg|gif|png|bmp))(?:\|class=(?P<class>[^|!]+))?(?:\|style=(?P<style>[^|!]+))?(?:\|alt=(?P<alt>[^|!]+))?(?:\|link=(?P<link>[^|!]+))?!/i', $para, $m, PREG_OFFSET_CAPTURE)) {
				$offset = 0;
				foreach ($m[0] as $index => $source) {
					if ($source[1] > $offset) {
						$text = substr($para, $offset, $source[1]-$offset);
						$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
					}
					
					$imgContainer = $paraCtl;
					
					if (!empty($m['url'][$index][0])) {
						$params = array('src' =>  $m['url'][$index][0]);
						if (!empty($m['style'][$index][0])) {
							$params['style'] = $m['style'][$index][0];
						}
						if (!empty($m['class'][$index][0])) {
							$params['class'] = $m['class'][$index][0];
						}
						if (!empty($m['alt'][$index][0])) {
							$params['alt'] = $m['alt'][$index][0];
						}
						if (!empty($m['link'][$index][0])) {
							$contParams['href'] = $m['link'][$index][0];
							if (!empty($m['target'][$index][0])) {
								$contParams['target'] = $m['target'][$index][0];
							}
							$imgContainer = $imgContainer->children[] = zing::create('THtmlLink', $contParams);
							if (isset($params['alt'])) {
								$imgContainer->setTitle($params['alt']);
							}
						}
						$imgContainer->children[] = zing::create('THtmlImage', $params);
					}
					$offset = $source[1] + strlen($source[0]);
				}
				if ($offset < strlen($para)) {
					$text = substr($para, $offset);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
				}
				continue;
		
			}

            /**
			 * == EMAIL AND WEBSITE LINKS
			 *
			 * user@domain.com|"Inner Text"
			 * www.domain.com|"Inner Text"
			 */
			
			$email = '[A-Z0-9-_&]+(\.[A-Z0-9-_&]+)*@[A-Z0-9-]+(\.[A-Z0-9-]+)+';
			$web = '((([A-Z]+:\/\/)|www\.)[A-Z0-9-_]+(\.[A-Z0-9][A-Z0-9-_]+)+(:\d+)?|((?<=\s|^)\/[^\/\?\s\|]+)|(#[\w\d\-_]+))(\/[^\/\?\s\|#]+)*(\?[^\s\|]*)?';
			if (preg_match_all('/((?:(?P<email>'.$email.')|(?P<web>'.$web.'))(?:\|(?:target=(?P<target>[\w_][\w_\-\d]+)\|)?\"(?P<inner>[^\"]+)\")?|(?P<web2>\/)(?:\|(?:target=(?P<target2>[\w_][\w_\-\d]+)\|)?\"(?P<inner2>[^\"]+)\"))/mi',$para, $m, PREG_OFFSET_CAPTURE)) {
				$offset = 0;
				foreach ($m[0] as $index => $source) {
					if ($source[1] > $offset) {
						$text = substr($para, $offset, $source[1]-$offset);
						$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
						$offset = $source[1] + strlen($source[0]);
					}
					if (!empty($m['email'][$index][0])) {
						$email = zing::encodeHex( $m['email'][$index][0] );
						$emailText = trim($m['inner'][$index][0]);
						if (empty($emailText)) {
							$emailText = '@@' . zing::encodeEntity( $m['email'][$index][0] ) . '@@';
						}
						$paraCtl->children[] = zing::create('THtmlLink', array('innerText' => $emailText, 'href' => 'mailto:' . $email));
					} else {
						$suffix = '';
						if (isset($m['web2'][$index][0])) {
							$suffix = '2';
						}
						$target = '';
						if (isset($m['target'][$index][0]) || isset($m['target2'][$index][0])) {
							$target = $m['target'][$index][0] . $m['target2'][$index][0];
						}
						$web = $m['web'.$suffix][$index][0];
						if (strstr($web,'://') === false && $web[0] != '/' && $web[0] != '#') {
							$web = 'http://' . $web;
						}
						$webText = $m['inner'.$suffix][$index][0];
						if (empty($webText)) {
							$webText = $m['web'.$suffix][$index][0];
						}
						$params =  array('innerText' => $webText, 'href' => $web);
						if (!empty($target)) {
							$params['target'] = $target;
						}
						$paraCtl->children[] = zing::create('THtmlLink', $params);
					}
					$offset = $source[1] + strlen($source[0]);
				}
				if ($offset < strlen($para)) {
					$text = substr($para, $offset);
					$paraCtl->children[] = zing::create('THtmlFormattedDiv', array('visible' => self::VIS_CHILDREN, 'innerText' => $text, 'paragraph' => false));
				}
				continue;
			}		
			
			$paraCtl->setInnerText($para);
		}
		
		$top->doStatesUntil('preRender');
	}
	
	
}

?>
