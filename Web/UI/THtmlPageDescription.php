<?php 

class THtmlPageDescription extends THtmlHeadComponent {

	public function updatePlaceholder($ph) {
		$title = $ph->getTitle();
	
		$ph->setDescription($this->getInnerText());
	}
}

?>