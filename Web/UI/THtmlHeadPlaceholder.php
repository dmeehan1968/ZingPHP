<?php

/**
 * THtmlHeadPlaceholder
 *
 * Tag used to define the position of the <head> element
 *
 * You can include decendants of THtmlHeadComponent as children
 * to the THtmlHeadPlaceholder tag, e.g.
 *
 * <zing:THtmlHeadPlaceholder>
 *   <zing:THtmlPageTitle>Page Title</zing:THtmlPageTitle>
 * </zing:THtmlHeadPlaceholder>
 *
 */

class THtmlHeadPlaceholder extends TCompositeControl {

	public function __construct($params = array()) {
		parent::__construct($params);
	}

	private $title;

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function hasTitle() {
		return isset($this->title);
	}

	private $description;

	public function setDescription($desc) {
		$this->description = $desc;
	}

	public function getDescription() {
		return $this->description;
	}

	public function hasDescription() {
		return isset($this->description);
	}

	private $content;

	public function setContent($content) {
		$this->content = $content;
	}

	public function getContent() {
		return $this->content;
	}

	public function hasContent() {
		return isset($this->content);
	}

	/**
	 * === SCRIPTS ===
	 */

	private 	$scripts = array();

	public function addScript(THtmlScript $script) {

		// prevent duplicate script insertion by indexing on the src file if
		// specified, or a hash of the script code

		if ($script->hasSrc()) {
			$hash = $script->getSrc();
		} else {
			$hash = md5($script->getInnerText());
		}

		$this->scripts[$hash] = $script;
	}

	/**
	 * === STYLES ===
	 */

	private	$styles = array();

	public function addStyle(THtmlStyle $style) {

		// prevent duplicate insertion by indexing on href attribute, or
		// hash of the content

		if ($style->hasHref()) {
			$hash = $style->getHref();
		} else {
			$hash = md5($style->getInnerText());
		}

		$this->styles[$hash] = $style;
	}

	/**
	 * === KEYWORDS ===
	 */

	private $stopWords = array('the', 'and', 'of', 'in', 'to', 'will', 'or', 'there', 'be', 'a', 'is', 'an', 'take', 'any', 'by', 'see',
		'this', 'with', 'need', 'on', 'are', 'new', 'up', 'down', 'when', 'have', 'that', 'do', 'all', 'where', 'as', 'whose', 'if',
		'for', 'her', 'my', 'i', 'from', 'has', 'at', 'also', 'she', 'his', 'made', 'some', 'he', 'him', 'over', 'been', 'am', 'them', 'use',
		'many', 'most', 'your', 'it', 'etc', 'more', 'those', 'each', 'who', 'can', 'use', 'own', 'into', 'become', 'their', 'was',
		'yes', 'no', 'we', 'but', 'these', 'being', 'what', 'our', 'so', 'its', 'ours', 'much', 'like', 'here', 'still', 'other',
		'put', 'you', 'one', 'two', 'seen');

	private $keywords = array();

	public function setKeywords($keywords = array()) {
		if (is_string($keywords)) {
			$keywords = explode(',', $keywords);
		}

		foreach ($keywords as $keyword) {
			$keyword = strtolower(trim($keyword));
			if (array_search($keyword, $this->stopWords) === false) {
				$this->keywords[$keyword] += (array_search($keyword,$this->getPremiumKeywords()) === false ? 1 : $this->getKeywordLoading());
			}
		}
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function hasKeywords() {
		return count($this->keywords);
	}

	private $keywordLoading = 3;

	public function setKeywordLoading($loading) {
		$this->keywordLoading = $loading;
	}

	public function getKeywordLoading() {
		return $this->keywordLoading;
	}

	private $premiumKeywords = array();

	public function setPremiumKeywords($keywords = array()) {
		if (is_string($keywords)) {
			$keywords = explode(',', $keywords);
		}

		foreach ($keywords as $keyword) {
			$keyword = strtolower(trim($keyword));
			$this->premiumKeywords[$keyword] = true;
		}
	}

	public function getPremiumKeywords() {
		return array_keys($this->premiumKeywords);
	}

	public function hasPremiumKeywords() {
		return count($this->premiumKeywords);
	}

	private $keywordThreshold = 1;

	public function setKeywordThreshold($th) {
		$this->keywordThreshold = $th;
	}

	public function getKeywordThreshold() {
		return $this->keywordThreshold;
	}

	public function render() {

		TControl::render();

		$top = $this->getTopControl();
		$controls = $top->getDescendantsByClass('THtmlHeadComponent');

		foreach ($controls as $control) {
			if ($control->isVisible()) {
				$control->updatePlaceholder($this);
			}
		}

		echo "<head>\n";
		if ($this->hasContent()) {
			echo $this->getContent();
		}
		if ($this->hasTitle()) {
			echo "\t<title>" . htmlentities($this->getTitle()) . "</title>\n";
		}
		if ($this->hasDescription()) {
			echo "\t<meta name=\"description\" content=\"" . htmlentities($this->getDescription()) . "\" />\n";
		}
		if ($this->hasKeywords()) {
			$keywords = $this->getKeywords();
			arsort($keywords);
			foreach ($keywords as $keyword => $weight) {
				if ($weight < $this->getKeywordThreshold()) {
					unset($keywords[$keyword]);
				}
			}
			echo "\t<meta name=\"keywords\" content=\"" . implode(',',array_keys($keywords)) . "\" />\n";
		}

		foreach ($this->styles as $style) {
			$old = $style->getRenderContent();
			$style->setRenderContent(true);
			echo "\t";
			$style->render();
			echo "\r\n";
			$style->setRenderContent($old);
		}

		foreach ($this->scripts as $script) {
			$old = $script->getRenderContent();
			$script->setRenderContent(true);
			echo "\t";
			$script->render();
			echo "\r\n";
			$script->setRenderContent($old);
		}
		echo "</head>\n";
	}

}

?>
