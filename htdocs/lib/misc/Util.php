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
		$stag_replacement = '<' . $tag . '>';
		$etag_replacement = '</' . $tag . '>';

		return str_replace(array($stag, $etag), array($stag_replacement, $etag_replacement), $subject);
	}

	public static function TagMatch($subject, $tag, $replacement=NULL) {

	}

	public static function PurifyTag($subject, $tag) {

		$open_tag = '<' . $tag . '>';
		$open_tag_length = strlen($open_tag);
		$close_tag = '</' . $tag . '>';
		$close_tag_length = strlen($close_tag);
		$completed_tag = $open_tag . $close_tag;
		$completed_tag_length = strlen($completed_tag);

		$continuing = True;
		$last_open_pos = 0;
		$last_close_pos = 0;

		$count = 0;

		while($continuing) {

			if ($count >= 5) {
				break;
			}

			$cur_open_pos = strpos($subject, $open_tag, $last_open_pos);
			$cur_close_pos = strpos($subject, $close_tag, $last_close_pos);

			// END CONDITION
			//
			if ($cur_open_pos === False && $cur_close_pos === False) {
				$continuing = False;
			}

			// MISMATCHES
			//
			// 1) If no more open tags
			//
			else if ($cur_open_pos === False && $cur_close_pos !== False) {

				// Fix rest of closing position tags
				//
				//$subject = substr_replace($subject, $completed_tag, $cur_close_pos);
				$subject = Util::StrReplaceAtPosition($close_tag, $completed_tag, $subject, $cur_close_pos);
				$continuing = False;
			}

			// 2) If no more closing tags
			//
			else if ($cur_open_pos !== False && $cur_close_pos === False) {

				// Fix rest of opening position tags
				//
				//$subject = substr_replace($subject, $open_tag, $cur_open_pos);
				$subject = Util::StrReplaceAtPosition($open_tag, $completed_tag, $subject, $cur_open_pos);
				$continuing = False;
			}

			// 3) Found closing tag before opening tag
			//
			else if ($cur_open_pos > $cur_close_pos) {

				// Fix closing tag
				$subject = substr_replace($subject, $completed_tag, $cur_close_pos, $close_tag_length);

				// Move back opening tag so that we find it again
				$last_open_pos = $cur_open_pos + $open_tag_length;
				$last_close_pos = $cur_close_pos + $open_tag_length + 1;
			}
			else if ($cur_open_pos < $cur_close_pos) {

				// check and see if there are any more open tags between current positions
				//
				// if so, you must close the preceding open tag and keep searching
				//
				$between_tag_found = False;
				$between_open_pos = strpos($subject, $open_tag, $cur_open_pos + 1);

				while ($between_open_pos < $cur_close_pos && $between_open_pos !== False) {

					if (!$between_tag_found)
						$between_tag_found = True;

					// update string
					// 
					$subject = Util::StrReplaceAtPosition($open_tag, $completed_tag, $subject, $cur_open_pos, $cur_open_pos + $open_tag_length);

					// look for next tag
					$between_open_pos = strpos($subject, $open_tag, $between_open_pos + 1);
					$next_open_pos = strpos($subject, $open_tag, $between_open_pos + 1);

					// update position counts
					$cur_open_pos = $between_open_pos;
					$cur_close_pos += $close_tag_length - 1;

					if ($next_open_pos > $cur_close_pos || $next_open_pos === False) {

						// subtracting by one because of the +1 down below
						//
						// we're essentially starting at a new zero
						//
						$cur_open_pos = $next_open_pos - 1;
						$cur_close_pos = $next_open_pos - 1;
						break;
					}
				}

				$last_open_pos = $cur_open_pos + 1;
				$last_close_pos = $cur_close_pos + 1;
			}

			$count++;
		}

		return $subject;
	}

	public static function StrReplaceAtPosition($tag, $tag_fix, $subject, $position, $end=NULL) {

		if ($end != NULL) {

			$substr_length = $end - $position;

			// get the relevant substring
			$str = substr($subject, $position, $substr_length);
		}
		else {
			// get the relevant substring
			$str = substr($subject, $position);
		}


		// replace all offending things with correct things in the substring
		$replacement = str_replace($tag, $tag_fix, $str);

		if ($end != NULL) {
			// insert replacement into the subject @ location
			return substr_replace($subject, $replacement, $position, $substr_length);
		}
		else {
			// insert replacement into the subject @ location
			return substr_replace($subject, $replacement, $position);
		}
	}
}

?>
