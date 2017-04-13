<?php

class TwitterMentionButton extends THtmlDiv {

	protected $user;

	public function setUser($user) {
		$this->user = $user;
	}

	public function getUser() {
		return $this->user;
	}

	public function hasUser() {
		return isset($this->user);
	}

	protected $showUser = false;

	public function setShowUser($show) {
		$this->showUser = zing::evaluateAsBoolean($show);
	}

	public function getShowUser() {
		return $this->showUser;
	}

	protected $size = 'small';

	public function setSize($size) {
		if (strcasecmp($size,'small') == 0) {
			$this->size = 'small';
		} else {
			$this->size = 'large';
		}
	}

	public function getSize() {
		return $this->size;
	}

	protected $text;

	public function setText($text) {
		$this->text = $text;
	}

	public function getText() {
		return $this->text;
	}

	public function hasText() {
		return isset($this->text);
	}

	protected $link;
	protected $script;

	public function init() {
		parent::init();

		$this->setTag('span');
		$this->setClass('twitter-mention-button');
		$this->link = $this->children[] = zing::create('THtmlLink', array('class' => 'twitter-mention-button'));

		$this->script = $this->children[] = zing::create('THtmlScriptInline', array('innerText' => '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");'));
	}

	public function render() {
		
		if ($this->hasUser()) {
			$this->link->setInnerText($this->getShowUser() ? 'Tweet about @' . $this->getUser() : 'Tweet this');
			$this->link->setHref('https://twitter.com/intent/tweet?screen_name=' . $this->getUser() . ($this->hasText() ? '&text=' . urlencode($this->getText()) : ''));
			$this->link->attributes['data-size'] = $this->getSize();
			$this->link->attributes['data-related'] = $this->getUser();
			parent::render();
		}
	}

}

?>
