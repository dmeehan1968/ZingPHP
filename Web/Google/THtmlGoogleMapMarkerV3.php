<?php

class THtmlGoogleMapMarkerV3 extends TCompositeControl {

	private $lat = 0;

	public function setLat($lat) {
		$this->lat = (float) $lat;
	}

	public function getLat() {
		return $this->lat;
	}

	private $lon = 0;

	public function setLng($lng) {
		$this->lng = (float) $lng;
	}

	public function getLng() {
		return $this->lng;
	}

	private $boundLatitude;

	public function setBoundLatitude($boundLatitude) {
		$this->boundLatitude = $boundLatitude;
	}

	public function getBoundLatitude() {
		return $this->boundLatitude;
	}

	public function hasBoundLatitude() {
		return isset($this->boundLatitude);
	}

	private $boundLongitude;

	public function setBoundLongitude($boundLongitude) {
		$this->boundLongitude = $boundLongitude;
	}

	public function getBoundLongitude() {
		return $this->boundLongitude;
	}

	public function hasBoundLongitude() {
		return isset($this->boundLongitude);
	}

	private $infoWindow;

	public function setInfoWindow($content) {
		if (!empty($content)) {
			$this->infoWindow = trim(str_replace("\n", ' ', $content));
		} else {
			unset($this->infoWindow);
		}
	}

	public function getInfoWindow() {
		return $this->infoWindow;
	}

	public function hasInfoWindow() {
		return isset($this->infoWindow);
	}

	private $onDragEnd;

	public function setOnDragEnd($function) {
		$this->onDragEnd = $function;
	}

	public function getOnDragEnd() {
		return $this->onDragEnd;
	}

	public function hasOnDragEnd() {
		return isset($this->onDragEnd);
	}

	private $marker;

	public function setMarker($marker) {
		if (is_numeric($marker)) {
			$this->marker = '/Zing/Assets/Images/GoogleMaps/Markers_Numeric/marker' . $marker . '.png';
		} else {
			$this->marker = $marker;
		}
	}

	public function getMarker() {
		return $this->marker;
	}

	public function hasMarker() {
		return isset($this->marker);
	}

	private $boundMarker;

	public function setBoundMarker($boundMarker) {
		$this->boundMarker = $boundMarker;
	}

	public function getBoundMarker() {
		return $this->boundMarker;
	}

	public function hasBoundMarker() {
		return isset($this->boundMarker);
	}

	public function bind() {
		if ($this->hasBoundObject()) {
			$object = $this->getBoundObject();
			if ($this->hasBoundLatitude()) {
				$this->setLat($this->resolveBoundValue($object, $this->getBoundLatitude()));
			}
			if ($this->hasBoundLongitude()) {
				$this->setLng($this->resolveBoundValue($object, $this->getBoundLongitude()));
			}
			if ($this->hasBoundMarker()) {
				$this->setMarker($this->resolveBoundValue($object, $this->getBoundMarker()));
			}
		}
	}

	public function render() {
		ob_start();
		parent::render();
		$output = ob_get_contents();
		ob_end_clean();

		$map = $this->getContainer('THtmlGoogleMapV3');
		if ($map) {
			$params = array();
			$params['lat'] = $this->getLat();
			$params['lng'] = $this->getLng();
			$params['infoWindow'] = '"' . trim(str_replace(array("\n", '"', "'"), array(" ", '\"', "\'"), $output)) . '"';
			if ($this->hasOnDragEnd()) {
				$params['onDragEnd'] = $this->getOnDragEnd();
			}
			if ($this->hasMarker()) {
				$params['icon'] = '"' . $this->getMarker() . '"';
			}

			$map->addMarker($params);
		}
	}

}


?>
