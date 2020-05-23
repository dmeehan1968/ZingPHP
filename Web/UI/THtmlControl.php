<?php

class THtmlControl extends TCompositeControl {

	private	$tag = 'div';
	public	$attributes;
	public	$rawAttributes;

	const		VIS_NONE = 0x00;
	const		VIS_TAGS = 0x01;
	const		VIS_CHILDREN = 0X02;
	const		VIS_ALL = 0xFF;

	public function __construct($params = array()) {
		$this->attributes = new TRegistry;
		$this->rawAttributes = new TRegistry;

		parent::__construct($params);

		$this->setVisible($this->getVisible());
	}

	public function getTag() {
		return $this->tag;
	}

	public function setTag($tag) {
		$this->tag = $tag;
	}

	public function setName($name) {
		$this->attributes['name'] = $name;
	}

	public function getName() {
		return $this->attributes['name'];
	}

	public function hasName() {
		return isset($this->attributes['name']);
	}

	public function setClass($class) {
		$this->attributes['class'] = strtolower($class);
	}

	public function getClass() {
		return $this->attributes['class'];
	}

	public function hasClass($class = null) {
		if (is_null($class)) {
			return isset($this->attributes['class']);
		} else {
			$classes = explode($this->attributes['class']);
			return in_array(strtolower($class), $classes);
		}
	}

	public function addClass($class) {
		$toAdd = explode(' ', strtolower($class));
		$existing = explode(' ', $this->attributes['class']);
		while (isset($existing[0]) && empty($existing[0])) {
			array_shift($existing);
		}

		foreach ($toAdd as $class) {
			if ( ! in_array($class, $existing)) {
				$existing[] = $class;
			}
		}
		$this->attributes['class'] = implode(' ', $existing);
	}

	public function removeClass($class) {
		$toRemove = explode(' ', $class);
		$existing = explode(' ', $this->attributes['class']);

		foreach ($toRemove as $class) {
			if (($pos = array_search($class, $existing)) !== false) {
				unset($existing[$pos]);
			}
		}

		$this->attributes['class'] = implode(' ', $existing);
	}

	public function setStyle($style) {
		$this->attributes['style'] = $style;
	}

	public function getStyle() {
		return $this->attributes['style'];
	}

	public function hasStyle() {
		return isset($this->attributes['style']);
	}

	public function setVisible($value) {
		if ($value === true) {
			parent::setVisible(self::VIS_ALL);
		} else if ($value === false) {
			parent::setVisible(self::VIS_NONE);
		} else {
			parent::setVisible($value);
		}
	}

	public function setId($id) {
		parent::setId($id);
		if (is_null($id)) {
			unset($this->attributes['id']);
		} else {
			$this->attributes['id'] = $id;
		}
	}

	public function render() {

		TControl::render();

		if ($this->hasPermission()) {

			$origAttributes = clone $this->attributes;

			foreach ($this->attributes as $index => $attrib) {
				$this->attributes[$index] = $this->onBindAttribute($index, $attrib);
			}

			if ($this->getVisible() & self::VIS_TAGS) {
				$this->renderPreChildren();
			}

			if ($this->getVisible() & self::VIS_CHILDREN) {
				$this->renderChildren();
			}

			if ($this->getVisible() & self::VIS_TAGS) {
				$this->renderPostChildren();
			}

			$this->attributes = $origAttributes;

		}
	}

	public function onBindAttribute($attribute, $value) {
		$origValue = $value;
		list($action, $value) = explode(':', $value);

		if (!isset($value)) {
			$value = $action;
			$action = null;
		}
		switch (strtolower($action)) {
			case 'bind':
				if ($this->hasBoundObject()) {
					return $this->resolveBoundValue($this->getBoundObject(), $value);
				}
				break;
			default:
				$value = $origValue;
				break;
		}

		return $value;
	}

	/**
	 * Collapse: determine if the control is rendered in abbreviated form, or
	 * has open/close tags
	 */

	private $collapse = true;

	public function setCollapse($coll) {
		$this->collapse = zing::evaluateAsBoolean($coll);
	}

	public function getCollapse() {
		return $this->collapse;
	}

	/**
	 * hasInnerContent
	 *
	 * Determine if the current control has inner content, or if it should be
	 * rendered as such (ie. both opening and closing tags)
	 */

	public function hasInnerContent() {
		if ($this->getCollapse()) {
			return $this->children->count() > 0 ? true : false;
		}
		return true;
	}

	public function renderPreChildren() {
		echo '<' . $this->getTag();
		foreach ($this->attributes as $attr => $value) {
			echo ' ' . htmlentities($attr) .'="' . htmlentities($value) . '"';
		}

		foreach ($this->rawAttributes as $attr => $value) {
			echo ' ' . htmlentities($attr) .'="' . $value . '"';
		}

		echo ($this->hasInnerContent() ? '' : ' /') . '>';
	}

	public function renderPostChildren() {
		if ($this->hasInnerContent()) {
			echo '</' . $this->getTag() . '>';
		}
	}

	private $callbacks;

	public function setCallbacks($callbacks) {
		if (is_array($callbacks)) {
			$this->callbacks = implode(',', $callbacks);
		} else {
			$this->callbacks = $callbacks;
		}
	}

	public function getCallbacks() {
		return explode(',',$this->callbacks);
	}

	public function hasCallbacks() {
		return isset($this->callbacks);
	}

	public function setInnerText($text, $forceEmpty = false) {
		$ctl = null;

		foreach ($this->children as $index => $child) {
			if ($child instanceof THtmlInnerText) {
				if (empty($text)) {
					unset($this->children[$index]);
					return;
				}
				$ctl = $child;
				break;
			}
		}

		if (! $forceEmpty && empty($text)) {
			return;
		}

		if (is_null($ctl)) {
			$ctl = $this->children[] = zing::create('THtmlInnerText');
			if ($this->hasCallbacks()) {
				$ctl->setCallbacks($this->getCallbacks());
			}
		}
		$ctl->setValue($text, $forceEmpty);
	}

	public function getInnerText() {

		$text = null;

		foreach ($this->children as $child) {
			if ($child instanceof THtmlInnerText) {
				$text .= $child->getValue();
			}
		}

		return $text;
	}

	public function hasInnerText() {
		$text = $this->getInnerText();
		return !empty($text);
	}

	public function bind() {
		if ($this->hasBoundProperty() && $this->hasBoundObject()) {
			$property = $this->getBoundProperty();
			$object = $this->getBoundObject();
			$value = TControl::resolveBoundValue($object, $property);

			$this->setInnerText($value);
		}

		parent::bind();
	}

	public function setOnClick($onclick) {
		$this->attributes['onclick'] = $onclick;
	}

	public function getOnClick() {
		return $this->attributes['onclick'];
	}

	public function hasOnClick() {
		return isset($this->attributes['onclick']);
	}

}


?>
