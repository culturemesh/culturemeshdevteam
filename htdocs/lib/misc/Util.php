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
				  $n = $interval->$props[$i];
				else
				  $n = $interval->$props[$i];

				return $n . ' ' . $jones[$i];
			}
		}
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

	/*
	 * Pulls out a string based on surrounding tags
	 *
	 * @returns - array(replacement string, extractions)
	 * @expects - tag name, tags enclosed by square brackets, html style [tag]content[/tag]
	 * ** may change that if the function turns popular
	 *
	 */
	public static function StrExtract($subject, $tag) {

		$result = array(
			'replacement' => $subject, 
			'extractions' => array()
		);

		$stag = '[' . $tag . ']';
		$etag = '[/' . $tag . ']';
		$tag_len = strlen($tag); // important for substring
		$stag_len = strlen($stag);
		$etag_len = strlen($etag);

		// loop through string, looking for tags
		$offset = 0;

		$str = $subject;
		$count = 0;

		while (($open = strpos($str, $stag, $offset)) !== false) {

			if ($count > 10)
				break;

			// found a tag, look for the end
			if (($close = strpos($str, $etag, $open+1)) !== false) {

				// get substring 
				$start = $open + $stag_len;
				$length = $close - $start;

				$target = substr($str, $start, $length);

				array_push($result['extractions'], $target);

				// rip out the thing
				$str = substr_replace($str, '', $start, $length);

				// replace str
				$result['replacement'] = $str;

				// increment offset
				$offset = $start + $etag_len;
				$count++;

			}
			else {
				// increment offset
				$offset++;
				$count++;
			}
		}

		// string length must not change
		return $result;
	}

	public static function StrReform($subject, $tag, $elements) {

		$stag = '[' . $tag . ']';
		$etag = '[/' . $tag . ']';
		$tag_len = strlen($tag); // important for substring
		$stag_len = strlen($stag);
		$etag_len = strlen($etag);

		// loop through string, looking for tags
		$offset = 0;
		$i = 0;

		$str = $subject;

		// if we have open
		while (($open = strpos($str, $stag, $offset)) !== false) {

			// and closed tags
			if (($close = strpos($str, $etag, $open+1)) !== false) {
				
				// get substring start position 
				$start = $open + $stag_len;

				// insert element
				$str = substr_replace($str, $elements[$i], $start, 0);

				// increment i, offset
				$i++;
				$offset = $start;
			}
			else {  // skip this tag

				$offset++;
			}
		}

		return $str;
	}

	// Replace tags in string with html tags
	// eg tag: [tag]Example text[/tag]
	//
	// and replaces with html tag <tag>Example text</tag>
	public static function TagReplace($subject, $tag, $replacement=NULL) {

		// allows user to specify replacement
		if ($replacement == NULL)
			$replacement = $tag;

		$stag = '[' . $tag . ']';
		$etag = '[/' . $tag . ']';
		$tag_len = strlen($tag); // important for substring
		$stag_len = strlen($stag);
		$etag_len = strlen($etag);

		$str = NULL;

		// loop through string, looking for tags
		$offset = 0;
		$i = 0;

		$str = $subject;

		// if we have open
		while (($open = strpos($str, $stag, $offset)) !== false) {


			// if we're here, we've found a closing tag
			if (($close = strpos($str, $etag, $open+1)) !== false) {

				// so now we search for any other opening tags within the substring
				// captured by open and close. We count the amount found, and then
				// use strpos operations until we get to the end

				$substr = substr($str, $open, $close);

				$embed_tag_count = 0;

				$search_index = $open+1;
				
				while ($tag = strpos($substr, $stag, $search_index)) {

					$new_close = strpos($str, $etag, $close+1);

					// get new substring
					$substr = substr($str, $open+$tag, $new_close);

					// increment search so we don't get stuck
					$search_index = tag+1;
					$embed_tag_count++;
				}

				// with
			}
		}

		return $str;
	}
}

?>
