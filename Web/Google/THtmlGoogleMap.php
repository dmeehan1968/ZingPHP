<?php

class THtmlGoogleMap extends TCompositeControl {

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

		$api = 'http://maps.google.com/maps';

		$queryParams['file'] = 'api';
		$queryParams['v'] = '2';
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

		foreach ($this->markers as $marker) {
			$paramText = '';
			foreach ($marker as $name => $value) {
				$paramText .= (empty($paramText) ? '' : ', ') . $name . ': ' . $value;
			}
			$markers[] = "map.addOverlay(createMarker( { map: map, " . $paramText . " }));";
		}

		echo "<script type=\"text/javascript\">
			//<![CDATA[

			function createMarker(params) {
				var point = new GLatLng(params.lat, params.lng);
				var icon = new GIcon(G_DEFAULT_ICON);
				if (params.icon) {
					icon.image = params.icon;
				}
				var marker = new GMarker(point, { draggable: params.onDragEnd ? true : false, icon: icon });
				if (params.onDragEnd) {
					GEvent.addListener(marker, 'dragend', params.onDragEnd);
				}
				if (params.infoWindow.length) {
					GEvent.addListener(marker, 'click', function() {
						params.map.openInfoWindowHtml(point, params.infoWindow);
					});
				}
				return marker;
			}

			function createMap" . $this->mapId . "() {
				if (GBrowserIsCompatible()) {
					var map = new GMap2(document.getElementById('" . $this->mapDiv->getId() . "'));
					map.setCenter(new GLatLng(" . $this->getLat() . ", " . $this->getLng() . "), " . $this->getZoom() . ");
					map.setUIToDefault();

					" . implode("\n", $markers) . "
				}
				YAHOO.util.Event.addListener('window', 'unload', GUnload);
			}
			//]]>
			</script>";
	}

}

?>
