<?php
namespace misc;

class Util {

	/*
	 * hasStringKey()
	 *  - Checks if an array has string keys
	 */
	public static function hasStringKey($array) {
  		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}

	public static function getController($string) {
		$split = explode('#', $string);

		if (count($split) != 2) {
			throw new \Exception("{$string} is not a valid control string");
		}

		return array(
			'controller' => $split[0],
			'action' => $split[1]
		);
	}

	/*
	 * Takes a php DateInterval object
	 * returns a string representing new time
	 */
	public static function IntervalToPostTime($interval) {

		// properties for DateInterval
		$props = array('y', 'm', 'd', 'h', 'i', 's');
		$jones = array(
			'year(s) ago',
			'month(s) ago',
			'day(s) ago',
			'hour(s) ago',
			'minute(s) ago',
			'second(s) ago'
		);

		for ($i = 0; $i < count($props); $i++) {

			// if value is empty, move on
			if ($interval->$props[$i] == 0)
				// check if we're talking seconds
				if (count($props) - $i == 1) {
					return 'just now';
				}
				else
				continue;
			else {
				// return first property with a nonzero value assigned
				// attach designation( month, year, etc)
				if ($props[$i] == 'i')
				  $n = 60 - $interval->$props[$i];
				else
				  $n = $interval->$props[$i];

				return $n . ' ' . $jones[$i];
			}
		}

		/*
		$incrms = array(
			60, 60, 24, 30, 12);	// seconds, minutes, hours, days, months	

		$jones = array(
			'just now',
			'second(s) ago',
			'minute(s) ago',
			'hour(s) ago',
			'day(s) ago',
			'month(s) ago',
			'year(s) ago'
		);

		$units = $interval->format(''); // units start as seconds, are divided

		for($i = 0; $i < count($incrms); $i++) {

			$incr = $incrms[$i];

			// less than
			if ($units / $incr <= 0) {
				if ($i == 0) {
					return $jones[$i];
				}

				return $units . ' ' . $jones[$i];
			}
			// equal to
			else if ($units / $incr < 1) {
				return $units . ' ' . $jones[$i+1];
			} else {
				$units = floor( $units / $incr);
			}
		}
		 */
	}

	// taking an array from network with a certain set of keys,
	// flipping them into a searchable
	public static function ArrayToSearchable($arr) {

		// if origin -> could be language or location
		// else -> is location

		// location
		//  -> could be city, region, or country, or error
		//  	-> if city is NULL, it could be region or country
		//  	  -> if region is NULL AND city is null, it could be country or error
		//  	  -> 
		//
		$keys = array_keys($arr);

		// we're dealing with an origin
		if (strpos($keys[0], 'origin') !== false) {

			if (isset($arr['language_origin']) && $arr['language_origin'] != NULL) {
				$lang = new \dobj\Language();
				//$lang->id = (int) $arr['id_language_origin'];
				$lang->name = $arr['language_origin'];
				return $lang;
			}
			else if ($arr['city_origin'] != NULL) {
				$city = new \dobj\City();

				//$city->id = (int) $arr['id_city_origin'];
				$city->name = $arr['city_origin'];
				//$city->region_id = (int) $arr['id_region_origin'];
				$city->region_name = $arr['region_origin'];
				//$city->country_id = (int) $arr['id_country_origin'];
				$city->country_name = $arr['country_origin'];

				return $city;
			}
			else if ($arr['region_origin'] != NULL) {
				$region = new \dobj\Region();

				//$region->id = (int) $arr['id_region_origin'];
				$region->name = $arr['region_origin'];
				//$region->country_id = (int) $arr['id_country_origin'];
				$region->country_name = $arr['country_origin'];

				return $region;
			}
			else if ($arr['country_origin'] != NULL) {
				$country = new \dobj\Country();

				//$country->id = (int) $arr['id_country_origin'];
				$country->name = $arr['country_origin'];

				return $country;
			}
		}
		else  { // we've got a location 

			if ($arr['city_cur'] != NULL) {
				$city = new \dobj\City();

				//$city->id = (int) $arr['id_city_cur'];
				$city->name = $arr['city_cur'];
				//$city->region_id = (int) $arr['id_region_cur'];
				$city->region_name = $arr['region_cur'];
				//$city->country_id = (int) $arr['id_country_cur'];
				$city->country_name = $arr['country_cur'];

				return $city;
			}
			else if ($arr['region_cur'] != NULL) {
				$region = new \dobj\Region();

				//$region->id = (int) $arr['id_region_cur'];
				$region->name = $arr['region_cur'];
				//$region->country_id = (int) $arr['id_country_cur'];
				$region->country_name = $arr['country_cur'];

				return $region;
			}
			else if ($arr['country_cur'] != NULL) {
				$country = new \dobj\Country();

				//$country->id = (int) $arr['id_country_cur'];
				$country->name = $arr['country_cur'];

				return $country;
			}
		}
	}

	public static function GetUsername($vars) {

		$name = NULL;

		// last resort, email
		if (isset($event->email))
			$host = $event->email;

		// make host username
		if (isset($event->username))
			$host = $event->username;

		// prioritize names
		if (isset($event->first_name)) {
			$host = $event->first_name;

			if (isset($event->last_name))
				$host .= ' '.$event->last_name;
		}

		return $name;
	}
}

?>
