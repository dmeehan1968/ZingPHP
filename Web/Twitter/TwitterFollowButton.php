<?php

class TwitterFollowButton extends THtmlDiv {

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

	protected $showCount = false;

	public function setShowCount($show) {
		$this->showCount = zing::evaluateAsBoolean($show);
	}

	public function getShowCount() {
		return $this->showCount;
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
	protected $link;
	protected $script;

	public function init() {
		parent::init();

		$this->setTag('span');
		$this->setClass('twitter-follow-button');
		$this->link = $this->children[] = zing::create('THtmlLink', array('class' => 'twitter-follow-button'));

		$this->script = $this->children[] = zing::create('THtmlScriptInline', array('innerText' => '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");'));
	}

	public function render() {

		if ($this->hasUser()) {

			$this->link->setInnerText('Follow' . ($this->getShowUser() ? ' @' . $this->getUser() : ''));
			$this->link->setHref('http://twitter.com/' . $this->getUser());
			$this->link->attributes['data-show-count'] = $this->getShowCount() ? 'true' : 'false';
			$this->link->attributes['data-size'] = $this->getSize();
			parent::render();
		}
	}

}

?>
