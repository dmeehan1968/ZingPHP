<?php

class THtmlGoogleMapV3 extends TCompositeControl {

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

	public function bind() {
		if ($this->hasBoundObject()) {
			$object = $this->getBoundObject();
			if ($this->hasBoundLatitude()) {
				$this->setLat($this->resolveBoundValue($object, $this->getBoundLatitude()));
			}
			if ($this->hasBoundLongitude()) {
				$this->setLng($this->resolveBoundValue($object, $this->getBoundLongitude()));
			}
		}
	}

	private $zoom = 13;

	public function setZoom($zoom) {
		$this->zoom = $zoom;
	}

	public function getZoom() {
		return $this->zoom;
	}

	public function preInit() {

		$api = 'https://maps.googleapis.com/maps/api/js';

		$queryParams['key'] = $this->session->parameters['google.maps.key'];
		$queryParams['sensor'] = 'false';
		$query = '';
		foreach ($queryParams as $index => $value) {
			$query .= (empty($query) ? '' : '&') . $index . '=' . $value;
		}
		$uri = $api . '?' . $query;

		$this->mapId = zing::createControlId();
		$this->script = $this->children[] = zing::create('THtmlScript', array('src' => $uri));
		$this->mapDiv = $this->children[] = zing::create('THtmlDiv', array('id' => 'google-map-' . $this->mapId, 'class' => 'google-map', 'innerText' => 'Map Loading...'));
		$this->loader = $this->children[] = zing::create('TYuiLoader', array('require' => 'event'));
		$this->loader->setOnSuccess('createMap' . $this->mapId . '();');
		parent::preInit();
	}

	private $markers;

	public function addMarker($params = array()) {
		$this->markers[] = $params;
	}

	public function render() {

		parent::render();

		foreach ((array)$this->markers as $marker) {
			$paramText = '';
			foreach ($marker as $name => $value) {
				$paramText .= (empty($paramText) ? '' : ', ') . $name . ': ' . $value;
			}
			$markers[] = "createMarker(map, {" . $paramText . " });";
		}

		echo "<script type=\"text/javascript\">
			//<![CDATA[

			var infoWindow;
			function createMarker(map, params) {
				var markerOptions = {
					map: map,
					position: new google.maps.LatLng(params.lat, params.lng),
					icon: params.icon,
					draggable: params.onDragEnd ? true : false
				}
				var marker = new google.maps.Marker(markerOptions);
				if (params.onDragEnd) {
					google.maps.event.addListener(marker, 'dragend', params.onDragEnd);
				}
				if (params.infoWindow.length) {
					google.maps.event.addListener(marker, 'click', function() {
						if (infoWindow) infoWindow.close();
						else infoWindow = new google.maps.InfoWindow();

						infoWindow.setContent(params.infoWindow);
						infoWindow.open(map, marker);
					});
				}
				return marker;
			}

			function createMap" . $this->mapId . "() {
				var mapOptions = {
					center: new google.maps.LatLng(" . $this->getLat() . ", " . $this->getLng() . "),
					zoom: " . $this->getZoom() . ",
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var map = new google.maps.Map(document.getElementById('" . $this->mapDiv->getId() . "'), mapOptions);
				//map.setUIToDefault();

				" . implode("\n", (array)$markers) . "

				//YAHOO.util.Event.addListener('window', 'unload', GUnload);
			}
			//]]>
			</script>";
	}

}

?>
