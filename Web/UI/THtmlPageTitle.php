<?php 

class THtmlPageTitle extends THtmlHeadComponent {

	private $seperator = ' :: ';
	
	public function setSeperator($sep) {
		$this->seperator = $sep;
	}
	
	public function getSeperator() {
		return $this->seperator;
	}
	
	public function hasSeperator() {
		return isset($this->seperator);
	}
	
	private $order = true;
	
	public function setOrder($order) {
		$this->order = zing::evaluateAsBoolean($order);
	}
	
	public function getOrder() {
		return $this->order;
	}

	public function setTitle($title) {
		$this->setInnerText($title);
	}
	
	public function getTitle() {
		return $this->getInnerText();
	}
		
	public function updatePlaceholder($ph) {
		$title = $ph->getTitle();

		if ($this->getOverwrite()) {
			$title = $this->getTitle();
		} else {
			if (strlen($title)) {
				if ($this->getOrder()) {
					$title .= $this->getSeperator() . $this->getTitle();
				} else {
					$title = $this->getTitle(). $this->getSeperator() . $title;
				}
			} else {
				$title = $this->getTitle();
			}
		}
		
		$ph->setTitle($title);
	}
}

?>