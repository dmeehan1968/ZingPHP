<?php

define('PEAR_PATH', dirname(__FILE__) . '/' . 'vendor/pear');
ini_set('include_path', PEAR_PATH . '/' . 'pear-core-minimal/src' . ':' . ini_get('include_path'));
ini_set('include_path', PEAR_PATH . '/' . 'mail' . ':' . ini_get('include_path'));
ini_set('include_path', PEAR_PATH . '/' . 'net_smtp' . ':' . ini_get('include_path'));
ini_set('include_path', PEAR_PATH . '/' . 'net_socket' . ':' . ini_get('include_path'));

if (defined('ZING_AUTOLOAD')) {
	function __autoload($class) {
		return zing::__autoload($class);
	}
}

if (isset($_REQUEST['profiler'])) {
	define('ZING_PROFILER', zing::evaluateAsBoolean($_REQUEST['profiler']));
}

if (defined('ZING_PROFILER') && ZING_PROFILER) {
	require_once(dirname(__FILE__) . '/PhpQuickProfiler/classes/PhpQuickProfiler.php');
} else {
	// create a dummy instance of the PQP Console class
	class Console {
		public static function log() {	}
		public function logMemory() { }
		public function logError() { }
		public function logSpeed() { }
		public function getLogs() {	}
	}
}

require_once('Core/Interfaces.php');

class Zing {

	private static $instance;

	/**
	 * Get a reference to a single instance of the zing class
	 *
	 * @return Zing
	 *		a reference to the zing singleton
	 */
	public function getInstance() {
		if (is_null(self::$instance)) { 
			self::$instance = new Zing;
		}
		return self::$instance;
	}

	public static $aliases = array(
		'TApplication'					=> 'Core/TApplication.php',
		'TAuthCondition'				=> 'Core/TAuthCondition.php',
		'TAuthentication'				=> 'Core/TAuthentication.php',
		'TDateTime'						=> 'Core/TDateTime.php',
		'AuthUser'						=> 'Web/Auth/AuthUser.php',
		'AuthGroup'						=> 'Web/Auth/AuthGroup.php',
		'AuthRole'						=> 'Web/Auth/AuthRole.php',
		'AuthPerm'						=> 'Web/Auth/AuthPerm.php',
		'THtmlAuthVerify'				=> 'Web/Auth/THtmlAuthVerify.php',
		'THtmlAuthACLAdmin'				=> 'Web/Auth/THtmlAuthACLAdmin.php',
		'THtmlAuthUserList'				=> 'Web/Auth/THtmlAuthUserList.php',
		'THtmlAuthUserEdit'				=> 'Web/Auth/THtmlAuthUserEdit.php',
		'TCache'						=> 'Core/TCache.php',
		'TCachedLayout'					=> 'Core/TCachedLayout.php',
		'TCompositeControl'				=> 'Core/TCompositeControl.php',
		'TContentPlaceholder'			=> 'Core/TContentPlaceholder.php',
		'TControl'						=> 'Core/TControl.php',
		'TFileSystemIterator'			=> 'Core/TFileSystemIterator.php',
		'THtmlAbbr'						=> 'Web/UI/THtmlAbbr.php',
		'THtmlAssignmentGroup'			=> 'Web/UI/THtmlAssignmentGroup.php',
		'THtmlAttributeText'			=> 'Web/UI/THtmlAttributeText.php',
		'THtmlBodyComponent'			=> 'Web/UI/THtmlBodyComponent.php',
		'THtmlBodyPlaceholder'			=> 'Web/UI/THtmlBodyPlaceholder.php',
		'THtmlBr'						=> 'Web/UI/THtmlBr.php',
		'THtmlButton'					=> 'Web/UI/THtmlButton.php',
		'THtmlCheckbox'					=> 'Web/UI/THtmlCheckbox.php',
		'THtmlCheckboxCombo'			=> 'Web/UI/THtmlCheckboxCombo.php',
		'THtmlCheckboxGroup'			=> 'Web/UI/THtmlCheckboxGroup.php',
		'THtmlCheckboxGroupCombo'		=> 'Web/UI/THtmlCheckboxGroupCombo.php',
		'THtmlClearText'				=> 'Web/UI/THtmlClearText.php',
		'THtmlControl'					=> 'Web/UI/THtmlControl.php',
		'THtmlDefinition'				=> 'Web/UI/THtmlDefinition.php',
		'THtmlDiv'						=> 'Web/UI/THtmlDiv.php',
		'THtmlDivNotify'				=> 'Web/UI/THtmlDivNotify.php',
		'THtmlEmbedWMV'					=> 'Web/UI/THtmlEmbedWMV.php',
		'THtmlFileUpload'				=> 'Web/UI/THtmlFileUpload.php',
		'THtmlFileUploadCombo'			=> 'Web/UI/THtmlFileUploadCombo.php',
		'THtmlFeedbackForm'				=> 'Web/UI/THtmlFeedbackForm.php',
		'THtmlFlashPlayer'				=> 'Web/UI/THtmlFlashPlayer.php',
		'THtmlForm'						=> 'Web/UI/THtmlForm.php',
		'THtmlFormCombo'				=> 'Web/UI/THtmlFormCombo.php',
		'THtmlFormattedDiv'				=> 'Web/UI/THtmlFormattedDiv.php',
		'THtmlGoogleAnalytics'			=> 'Web/UI/THtmlGoogleAnalytics.php',
		'THtmlHeadComponent'			=> 'Web/UI/THtmlHeadComponent.php',
		'THtmlHeadKeywords'				=> 'Web/UI/THtmlHeadKeywords.php',
		'THtmlImage'					=> 'Web/UI/THtmlImage.php',
		'THtmlImageRotator'				=> 'Web/UI/THtmlImageRotator.php',
		'THtmlInlineScript'				=> 'Web/UI/THtmlInlineScript.php',
		'THtmlInnerText'				=> 'Web/UI/THtmlInnerText.php',
		'THtmlInput'					=> 'Web/UI/THtmlInput.php',
		'THtmlInputCombo'				=> 'Web/UI/THtmlInputCombo.php',
		'THtmlLabel'					=> 'Web/UI/THtmlLabel.php',
		'THtmlLink'						=> 'Web/UI/THtmlLink.php',
		'THtmlHeadPlaceholder'			=> 'Web/UI/THtmlHeadPlaceholder.php',
		'THtmlMonoSlideShow'			=> 'Web/UI/THtmlMonoSlideShow.php',
		'THtmlPager'					=> 'Web/UI/THtmlPager.php',
		'THtmlPlainTextCombo'			=> 'Web/UI/THtmlPlainTextCombo.php',
		'THtmlRadioButton'				=> 'Web/UI/THtmlRadioButton.php',
		'THtmlRadioCombo'				=> 'Web/UI/THtmlRadioCombo.php',
		'THtmlRadioGroup'				=> 'Web/UI/THtmlRadioGroup.php',
		'THtmlRadioGroupCombo'			=> 'Web/UI/THtmlRadioGroupCombo.php',
		'THtmlRandomAdvert'				=> 'Web/UI/THtmlRandomAdvert.php',
		'THtmlScript'					=> 'Web/UI/THtmlScript.php',
		'THtmlScriptInline'				=> 'Web/UI/THtmlScriptInline.php',
		'THtmlSelect'					=> 'Web/UI/THtmlSelect.php',
		'THtmlSelectCombo'				=> 'Web/UI/THtmlSelectCombo.php',
		'THtmlSelectOption'				=> 'Web/UI/THtmlSelectOption.php',
		'THtmlStyle'					=> 'Web/UI/THtmlStyle.php',
		'THtmlTable'					=> 'Web/UI/THtmlTable.php',
		'THtmlTableColumn'				=> 'Web/UI/THtmlTableColumn.php',
		'THtmlTableData'				=> 'Web/UI/THtmlTableData.php',
		'THtmlTableRow'					=> 'Web/UI/THtmlTableRow.php',
		'THtmlPageTitle'				=> 'Web/UI/THtmlPageTitle.php',
		'THtmlPageDescription'			=> 'Web/UI/THtmlPageDescription.php',
		'THtmlShadowbox'				=> 'Web/UI/THtmlShadowbox.php',
		'THtmlShadowboxImage'			=> 'Web/UI/THtmlShadowboxImage.php',
		'THtmlStandardPage'				=> 'Web/CMS/THtmlStandardPage.php',
		'THtmlStandardPageExtract'		=> 'Web/CMS/THtmlStandardPageExtract.php',
		'THtmlStandardPageIPE'			=> 'Web/CMS/THtmlStandardPageIPE.php',
		'THtmlYahooDropDownMenu'		=> 'Web/UI/THtmlYahooDropDownMenu.php',
		'THtmlYahooTabView'				=> 'Web/UI/THtmlYahooTabView.php',
		'CmsPage'						=> 'Web/CMS/CmsPage.php',
		'CmsSearch'						=> 'Web/CMS/CmsSearch.php',
		'THtmlCmsNavigation'			=> 'Web/CMS/THtmlCmsNavigation.php',
		'THtmlCmsBreadcrumb'			=> 'Web/CMS/THtmlCmsBreadcrumb.php',
		'THtmlFileFolderView'			=> 'Web/CMS/THtmlFileFolderView.php',
		'StaticOrNotFound'				=> 'Web/Static/StaticOrNotFound.php',
		'THtmlTextarea'					=> 'Web/UI/THtmlTextarea.php',
		'THtmlTextareaCombo'			=> 'Web/UI/THtmlTextareaCombo.php',
		'THtmlWriter'					=> 'Web/THtmlWriter.php',
		'TLayout'						=> 'Core/TLayout.php',
		'TLayoutMap'					=> 'Core/TLayoutMap.php',
		'TModule'						=> 'Core/TModule.php',
		'TModuleMap'					=> 'Core/TModuleMap.php',
		'TModuleMapBase'				=> 'Core/TModuleMap.php',
		'TModuleToUri'					=> 'Web/TModuleToUri.php',
		'TObjectCollection'				=> 'Object/TObjectCollection.php',
		'TObjectPersistence'			=> 'Object/TObjectPersistence.php',
		'TObjectPdoException'			=> 'Object/TObjectPdoException.php',
		'TPager'						=> 'Core/TPager.php',
		'TPaths'						=> 'Core/TPaths.php',
		'TParameters'					=> 'Core/TParameters.php',
		'TPlainText'					=> 'Core/TPlainText.php',
		'TRawOutput'					=> 'Core/TRawOutput.php',
		'TRegistry'						=> 'Core/TRegistry.php',
		'TRepeater'						=> 'Core/TRepeater.php',
		'TSession'						=> 'Core/TSession.php',
		'TSPLFileInfo'					=> 'Core/TSPLFileInfo.php',
		'TTemplateControl'				=> 'Core/TTemplateControl.php',
		'TTextWriter'					=> 'Core/TTextWriter.php',
		'IWriter'						=> 'Core/IWriter.php',
		'ZingPdo'						=> 'PDO/ZingPdo.php',
		'TPdoCollection'				=> 'PDO/TPdoCollection.php',
		'TPdoColumn'					=> 'PDO/TPdoColumn.php',
		'TPdoProxy'						=> 'PDO/TPdoProxy.php',
		'TPdoRelation'					=> 'PDO/TPdoRelation.php',
		'TPdoTable'						=> 'PDO/TPdoTable.php',
		'TYuiInPlaceEditor'				=> 'Web/YUI/TYuiInPlaceEditor.php',
		'TYuiLoader'					=> 'Web/YUI/TYuiLoader.php',
		'TYuiMenu'						=> 'Web/YUI/TYuiMenu.php',
		'TYuiMenuItem'					=> 'Web/YUI/TYuiMenuItem.php',
		'TYuiMenuBar'					=> 'Web/YUI/TYuiMenuBar.php',
		'TYuiMenuBarItem'				=> 'Web/YUI/TYuiMenuBarItem.php',

		'THtmlGoogleMap'				=> 'Web/Google/THtmlGoogleMap.php',
		'THtmlGoogleMapV3'				=> 'Web/Google/THtmlGoogleMapV3.php',
		'THtmlGoogleMapMarker'			=> 'Web/Google/THtmlGoogleMapMarker.php',
		'THtmlGoogleMapMarkerV3'			=> 'Web/Google/THtmlGoogleMapMarkerV3.php',
		'MultiMapGeocoder'				=> 'Libs/MultiMapGeocoder.php',
		
		'TextParser'					=> 'Text/TextParser.php',
		'TextParser_Parser'				=> 'Text/TextParser_Parser.php',
		'TextParser_Renderer'			=> 'Text/TextParser_Renderer.php',
		'TextParser_Rules'				=> 'Text/TextParser_Rules.php',

		'ClearText_Rules'					=> 'Text/ClearText_Rules.php',
		'ClearText_Parser_AbstractSpan'		=> 'Text/ClearText_Parser_AbstractSpan.php',
		'ClearText_Parser_Blockquote'		=> 'Text/ClearText_Parser_Blockquote.php',
		'ClearText_Parser_Definition'		=> 'Text/ClearText_Parser_Definition.php',
		'ClearText_Parser_Emphasis'			=> 'Text/ClearText_Parser_Emphasis.php',
		'ClearText_Parser_Entities'			=> 'Text/ClearText_Parser_Entities.php',
		'ClearText_Parser_Fractions'		=> 'Text/ClearText_Parser_Fractions.php',
		'ClearText_Parser_Heading'			=> 'Text/ClearText_Parser_Heading.php',
		'ClearText_Parser_LinkReferences'	=> 'Text/ClearText_Parser_LinkReferences.php',
		'ClearText_Parser_Objects'			=> 'Text/ClearText_Parser_Objects.php',
		'ClearText_Parser_Links'			=> 'Text/ClearText_Parser_Links.php',
		'ClearText_Parser_List'				=> 'Text/ClearText_Parser_List.php',
		'ClearText_Parser_Normalise'		=> 'Text/ClearText_Parser_Normalise.php',
		'ClearText_Parser_Paragraph'		=> 'Text/ClearText_Parser_Paragraph.php',
		'ClearText_Parser_Preformatted'		=> 'Text/ClearText_Parser_Preformatted.php',
		'ClearText_Parser_Strong'			=> 'Text/ClearText_Parser_Strong.php',
		'ClearText_Parser_Weblink'			=> 'Text/ClearText_Parser_Weblink.php',
		'ClearText_Parser_LineBreak'		=> 'Text/ClearText_Parser_LineBreak.php',

		'Xhtml_Render_AbstractSpan'		=> 'Text/Xhtml_Render_AbstractSpan.php',
		'Xhtml_Render_Blockquote'		=> 'Text/Xhtml_Render_Blockquote.php',
		'Xhtml_Render_Definition'		=> 'Text/Xhtml_Render_Definition.php',
		'Xhtml_Render_Emphasis'			=> 'Text/Xhtml_Render_Emphasis.php',
		'Xhtml_Render_Entities'			=> 'Text/Xhtml_Render_Entities.php',
		'Xhtml_Render_Fractions'		=> 'Text/Xhtml_Render_Fractions.php',
		'Xhtml_Render_Heading'			=> 'Text/Xhtml_Render_Heading.php',
		'Xhtml_Render_LinkReferences'	=> 'Text/Xhtml_Render_LinkReferences.php',
		'Xhtml_Render_Links'			=> 'Text/Xhtml_Render_Links.php',
		'Xhtml_Render_Objects'			=> 'Text/Xhtml_Render_Objects.php',
		'Xhtml_Render_List'				=> 'Text/Xhtml_Render_List.php',
		'Xhtml_Render_Normalise'		=> 'Text/Xhtml_Render_Normalise.php',
		'Xhtml_Render_Paragraph'		=> 'Text/Xhtml_Render_Paragraph.php',
		'Xhtml_Render_Preformatted'		=> 'Text/Xhtml_Render_Preformatted.php',
		'Xhtml_Render_Strong'			=> 'Text/Xhtml_Render_Strong.php',
		'Xhtml_Render_Weblink'			=> 'Text/Xhtml_Render_Weblink.php',
		'Xhtml_Render_LineBreak'		=> 'Text/Xhtml_Render_LineBreak.php',

		'TwitterFollowButton'			=> 'Web/Twitter/TwitterFollowButton.php', 
		'TwitterMentionButton'			=> 'Web/Twitter/TwitterMentionButton.php', 
);
	
	public static function addClass($class, $file) {
		if (substr($file,0,1) != '/') {
			$sess = TSession::getInstance();
			$file = $sess->paths->base . $file;
		}
		zing::$aliases[$class] = $file;
	}
	
	/**
	 * Class autoloader that maps class names to implementation files
	 *
	 * The Zing autoloader is designed to simplify inclusion of component files
	 * by accessing a central map of class names to implementation files, which 
	 * may be stored in seperate sub-directories for logical grouping.
	 *
	 * The autoload function can be enabled by defining ZING_AUTOLOAD prior to 
	 * inclusion of zing.php.
	 *
	 * If it is required to support autoload functionality for other aspects of 
	 * the project, zing::autoload() can be called from within the applications own
	 * __autoload() implementation.
	 *
	 * @param string $class
	 *		the name of the class to load
	 */
	
	public static function __autoload($class) {
	
		if (isset(zing::$aliases[$class])) {
			require_once zing::$aliases[$class];
		}		
	}
	
	public static function Uses($namespace) {
		if (is_string($namespace)) {
			$namespace = new TNamespace($namespace);
		}

		require_once $namespace->getClassFile();
		return $namespace;
	}

	public static function create($class, $params = null) {
		$instance = new $class($params);
		if (defined('ZING_TRACK_EVENTS') && ZING_TRACK_EVENTS) {
			if ($instance instanceof IObservable) {
				$instance->observers[] = zing::getInstance();
			}
		}
		return $instance;
	}

	public static function debug($var, $flush = false) {
		echo "<pre style=\"text-align: left\">" . htmlentities(print_r($var,true)) . "</pre>";
		if ($flush) {
			flush();
		}
	}

	private static $controlId = 0;
	
	public static function createControlId() {
		return self::$controlId++;
	}

	public $events = array();
	
	public function observedEvent($object, $event, $params) {
		list($micro, $time) = explode(' ',microtime());
		$tstr = strftime('%Y/%m/%d %H:%M',$time) . substr($micro,1);
		
		$this->events[] = array('time' => $tstr, 'times' => ((float)$time + (float)$micro), 'object' => $object, 'event' => $event, 'params' => $params);
	}

	public static function evaluateAsBoolean($value) {
		if (is_bool($value)) {
			return $value;
		}
		
		if (strcasecmp($value,'yes') == 0 || strcasecmp($value,'true') == 0 || (int) $value) {
			return true;
		}
		
		return false;
	}

	public static function str_truncate($str, $len) {
		if (strlen($str) < $len) {
			return $str;
		}
		$str = substr($str, 0, $len-3);
		return $str.'...';
	}
	
	public function urltext($string) {
		$output = '';
		$last = '-';
		$allowed = 'abcdefghijklmnopqrstuvwxyz01234567890';
		foreach (str_split(strtolower(trim($string))) as $char) {
			if (strpos($allowed, $char) !== false) {
				$output .= $char;
				$last = $char;
			} else {
				if ($last != '-') {
					$output .= $last = '-';
				}
			}
		}
		
		if ($last == '-') {
			$output = substr($output, 0, strlen($output)-1 );
		}
		return $output;
	}
	
	public static function encodeHex($str) {
		return '%' . substr( chunk_split( bin2hex( $str ), 2, '%'), 0, -1);
	}
	
	public static function encodeEntity($str) {
		return '&#x' . substr( chunk_split( bin2hex( $str ), 2, ';&#x'), 0, -3);
	}

	public static function emailLink($str) {
		if (preg_match('/^[A-Z0-9\-_]+(\.[A-Z0-9\-_]+)*@[A-Z0-9-]+(\.[A-Z0-9-]+)+$/i', $str)) {
			$link = zing::create('THtmlLink', array('href' => 'mailto:' . zing::encodeHex($str), 'innerText' => '@@' . zing::encodeEntity($str) . '@@'));
			ob_start();
			$link->doStatesUntil('renderComplete');
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
		return $str;
	}
		
	public static function webLink($str) {
		if (preg_match('/^([A-Z]+:\/\/)?[A-Z][A-Z0-9\-_]+(\.[A-Z0-9][A-Z0-9\-_]+)+(:\d+)?(\/[^\/\?\s]+)*(\?.*)?$/i', $str)) {
			$link = zing::create('THtmlLink', array('href' => (strstr($str,'://') === false ? 'http://' : '') . $str, 'innerText' => $str, 'target' => '_blank'));
			ob_start();
			$link->doStatesUntil('renderComplete');
			$result = ob_get_contents();
			ob_end_clean();
			return $result;
		}
		return $str;
	}

	public static function sqlDateToNatural($str, $format = 'D, jS F Y, H:ia') {
		$time = strtotime($str);
		return gmdate($format, $time);
	}
	
	public static function timeToSqlDateTime($time = null, $format = 'Y-m-d H:i:s') {
		if (is_null($time)) {
			$time = time();
		}
		return gmdate($format, $time);
	}
	
	public function convertArrayToObject($array, $class) {
		$objects = array();
		foreach ($array as $element) {
			$object = new $class;
			foreach ($element as $key => $value) {
				$object->$key = $value;
			}
			$objects[] = $object;
		}
		return $objects;
	}
			
	public function getParentPath($path) {
		$folders = explode('/', $path);
		array_pop($folders);
		return implode('/', $folders);
	}
}


?>
