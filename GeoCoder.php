<?php

    /**
	 * Universal free geocoding class. 
	 *
	 * Uses 3 API's: 
	 * - Google (2500/day max) 
	 * - MapQuest (seems to be unlimited, but doesn't always find adresses)
	 * - and GeoCodeFarm (2500/day, registration needed)
	 *
	 *@author Igor Timoshenkov (igor.timoshenkov@gmail.com)
	 */

  class GeoCoder {

		public static $log = array();

		public static $google_url = "http://maps.googleapis.com/maps/api/geocode/json?";
		public static $google_params = array(
			'address'	=> null,
			'sensor'	=> 'false'
		);

		public static $mapquest_url = "http://open.mapquestapi.com/geocoding/v1/address?";
		public static $mapquest_params = array(
			'inFormat'		=> 'kvp',
			'outFormat'		=> 'json',
			'location'		=> null,
			'maxResults'	=> 1
		);

		public static $geocodefarm_url = "http://geocodefarm.com/geo.php?";
		public static $geocodefarm_params = array(
			'key'	=> 'YOUR_API_KEY',
			'addr'	=> null,
		);

		/**
		 * Get geo coords using all available sources
		 *
		 * @param string $adress
		 * @param string $source
		 *
		 * @return mixed
		 */
		public static function get_coords($adress, $source = 'all')
		{
			switch ($source) {

				case 'google':
					return self::get_google_coords($adress);
				break;

				case 'mapquest':
					return self::get_mapquest_coords($adress);
				break;

				case 'geocodefarm':
					return self::get_deocodefarm_coords($adress);
				break;

				default:
				case 'all':
					// ask Google
					$google_coords = self::get_google_coords($adress);
					if (is_array($google_coords)) {
						return $google_coords;
					}
					else {
						// Log Google API error
						self::$log[] = $google_coords;

						// try MapQuest
						$mapquest_coors = self::get_mapquest_coords($adress);
						if (is_array($mapquest_coors)) {
							return $mapquest_coors;
						}
						else {
							// Log MapQuest API error
							self::$log[] = $mapquest_coors;

							// try GeocodeFarm
							return self::get_deocodefarm_coords($adress);
						}
					}
				break;
			}
		}

		/**
		 * Get coords via Google Geocoding API
		 *
		 * @param string $adress
		 *
		 * @return mixed
		 */
		protected static function get_google_coords($adress)
		{
			self::$google_params['address'] = $adress;
			$response = file_get_contents(self::$google_url.http_build_query(self::$google_params));

			if ($response !== false) {
				$data = json_decode($response);

				switch ($data->status)
				{
					case "OK":
						return array(
							'source'	=> 'Google',
							'lat'		=> $data->results[0]->geometry->location->lat,
							'lng'		=> $data->results[0]->geometry->location->lng,
						);
					break;

					case "OVER_QUERY_LIMIT":
						return "Unable to get the coords from Google - day limit reached (2500 queries). Please run this script tomorrow.";
					break;

					case "ZERO_RESULTS":
						return "No results for the address: ".$adress.".";
					break;

					default:
					case "UNKNOWN_ERROR":
						return "Google API unknown error.";
					break;
				}
			}
			else {
				return "Google API unknown error.";
			}
		}

		/**
		 * Get coords via MapQuest Geocoding API
		 *
		 * @param string $adress
		 *
		 * @return mixed
		 */
		protected static function get_mapquest_coords($adress)
		{
			self::$mapquest_params['location'] = $adress;
			$response = file_get_contents(self::$mapquest_url.http_build_query(self::$mapquest_params));

			if ($response !== false) {
				$data 		= json_decode($response);

				if (!empty($data->results[0]->locations) && isset($data->results[0]->locations[0]->latLng)) {
					return array(
						'source'	=> 'MapQuest',
						'lat'		=> $data->results[0]->locations[0]->latLng->lat,
						'lng'		=> $data->results[0]->locations[0]->latLng->lng,
					);
				}
				else {
					return "MapQuest API returned no data.";
				}
			}
			else {
				return "MapQuest API unknown error.";
			}
		}

		/**
		 * Get coords via GeocodeFarm API
		 *
		 * @param string $adress
		 *
		 * @return mixed
		 */
		protected static function get_deocodefarm_coords($adress)
		{
			if (!isset($geocodefarm_params['key'])) {
				self::$geocodefarm_params['addr'] = $adress;
				$response = file_get_contents(self::$geocodefarm_url.http_build_query(self::$geocodefarm_params));

				if ($response !== false) {
					$data = new SimpleXMLElement($response);

					if ($data->COORDINATES->Latitude != 0 && $data->COORDINATES->Longitude != 0) {
						return array(
							'source'	=> 'GeocodeFarm',
							'lat'		=> (float) $data->COORDINATES->Latitude[0],
							'lng'		=> (float) $data->COORDINATES->Longitude[0],
						);
					}
					else {
						return "GeocodeFarm - ".$adress." not found.";
					}
				}
				else {
					return "GeocodeFarm API unknown error.";
				}
			}
			else {
				return "GeocodeFarm API key not specified.";
			}
		}

	}
?>
