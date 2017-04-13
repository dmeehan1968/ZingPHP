<?php
/* 11-02-2013 DJM converted to use Google Maps v3 Geocoder
*/

class MultiMapGeocoder {

	public function postcode($postcode) {
	
		$postcode = preg_replace('/[^\w\d]/','',$postcode);

//		$url = 'http://mmw.multimap.com/API/geocode/1.2/public_api?output=xml&countryCode=GB&qs=' . $postcode;
		$url = 'http://maps.googleapis.com/maps/api/geocode/xml?address=' . $postcode . '&region=uk&sensor=false';
		
		if (ini_get('allow_url_fopen')) {
			$file_contents = @file_get_contents($url);
		} else {
			$ch = curl_init();
			$timeout = 5; // set to zero for no timeout
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
		}

	
		if (!empty($file_contents)) {
			$xml = new SimpleXMLElement($file_contents);
			if ($xml->status == 'OK') {
				return array($xml->result->geometry->location->lat, $xml->result->geometry->location->lng);
			}
		}
		
		return array();
	}

}

?>
