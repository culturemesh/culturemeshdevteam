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

	public static function Quote($string) {
		return '\'' . $string . '\'';
	}

	public static function DoubleQuote($string) {
		return "\"" . $string . "\"";
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

	/*
	 * DoubleMetaphone Functional 1.01 (altered)
	 * 
	 * DESCRIPTION
	 * 
	 * This function implements a "sounds like" algorithm developed
	 * by Lawrence Philips which he published in the June, 2000 issue
	 * of C/C++ Users Journal.  Double Metaphone is an improved
	 * version of Philips' original Metaphone algorithm.
	 * 
	 * COPYRIGHT
	 * 
	 * Slightly adapted from the class by Stephen Woodbridge.
	 * Copyright 2001, Stephen Woodbridge <woodbri@swoodbridge.com>
	 * All rights reserved.
	 * 
	 * http://swoodbridge.com/DoubleMetaPhone/
	 * 
	 * This PHP translation is based heavily on the C implementation
	 * by Maurice Aubrey <maurice@hevanet.com>, which in turn  
	 * is based heavily on the C++ implementation by
	 * Lawrence Philips and incorporates several bug fixes courtesy
	 * of Kevin Atkinson <kevina@users.sourceforge.net>.
	 * 
	 * This module is free software; you may redistribute it and/or
	 * modify it under the same terms as Perl itself.
	 * 
	 * 
	 * CONTRIBUTIONS
	 * 
	 * 2002/05/17 Geoff Caplan  http://www.advantae.com
	 *   Bug fix: added code to return class object which I forgot to do
	 *   Created a functional callable version instead of the class version
	 *   which is faster if you are calling this a lot.
	 * 
	 * 2013/05/04 Steen Rémi
	 *   New indentation of the code for better readability
	 *   Some small alterations
	 *   Replace ereg by preg_match
	 *     ( ereg : This function has been DEPRECATED as of PHP 5.3.0 )
	 *   Improve performance (10 - 20 % faster)
	 *
	 * 2014/11/07 Ross Kelly
	 *   Reported a bug with the oreg_match change that it needed delimiters
	 *   around the the regular expressions.
	 */
	public static function DoubleMetaphone($string) {

		$primary = '';
		$secondary = '';
		$current = 0;
		$length = strlen( $string );
		$last = $length - 1;
		$original = strtoupper( $string ).'     ';

		// skip this at beginning of word
		if (Util::StringAt($original, 0, 2, array('GN','KN','PN','WR','PS'))){
			$current++;
		}

		// Initial 'X' is pronounced 'Z' e.g. 'Xavier'
		if (substr($original, 0, 1) == 'X'){
			$primary   .= 'S'; // 'Z' maps to 'S'
			$secondary .= 'S';
			$current++;
		}

		// main loop

		while (strlen($primary) < 4 || strlen($secondary) < 4){
			if ($current >= $length){
				break;
			}

			// switch (substr($original, $current, 1)){
			switch ($original[$current]){
				case 'A':
				case 'E':
				case 'I':
				case 'O':
				case 'U':
				case 'Y':
					if ($current == 0){
						// all init vowels now map to 'A'
						$primary   .= 'A';
						$secondary .= 'A';
					}
					++$current;
					break;

				case 'B':
					// '-mb', e.g. "dumb", already skipped over ...
					$primary   .= 'P';
					$secondary .= 'P';

					if (substr($original, $current + 1, 1) == 'B'){
						$current += 2;
					} else {
						++$current;
					}
					break;

				case 'Ç':
					$primary   .= 'S';
					$secondary .= 'S';
					++$current;
					break;

				case 'C':
					// various gremanic
					if ($current > 1
					 && !Util::IsVowel($original, $current - 2)
					 && Util::StringAt($original, $current - 1, 3, array('ACH'))
					 && (
							(substr($original, $current + 2, 1) != 'I')
						 && (
								(substr($original, $current + 2, 1) != 'E')
							 || Util::StringAt($original, $current - 2, 6, array('BACHER', 'MACHER'))
							)
						)
					){
						$primary   .= 'K';
						$secondary .= 'K';
						$current += 2;
						break;
					}

					// special case 'caesar'
					if ($current == 0
					 && Util::StringAt($original, $current, 6, array('CAESAR'))
					){
						$primary   .= 'S';
						$secondary .= 'S';
						$current += 2;
						break;
					}

					// italian 'chianti'
					if (Util::StringAt($original, $current, 4, array('CHIA'))){
						$primary   .= 'K';
						$secondary .= 'K';
						$current += 2;
						break;
					}

					if (Util::StringAt($original, $current, 2, array('CH'))){

						// find 'michael'
						if ($current > 0
						 && Util::StringAt($original, $current, 4, array('CHAE'))
						){
							$primary   .= 'K';
							$secondary .= 'X';
							$current += 2;
							break;
						}

						// greek roots e.g. 'chemistry', 'chorus'
						if ($current == 0
						 && (
								Util::StringAt($original, $current + 1, 5, array('HARAC', 'HARIS'))
							 || Util::StringAt($original, $current + 1, 3, array('HOR', 'HYM', 'HIA', 'HEM'))
							)
						 && !Util::StringAt($original, 0, 5, array('CHORE'))
						){
							$primary   .= 'K';
							$secondary .= 'K';
							$current += 2;
							break;
						}

						// germanic, greek, or otherwise 'ch' for 'kh' sound
						if ((
								Util::StringAt($original, 0, 4, array('VAN ', 'VON '))
							 || Util::StringAt($original, 0, 3, array('SCH'))
							)
							// 'architect' but not 'arch', orchestra', 'orchid'
						 || Util::StringAt($original, $current - 2, 6, array('ORCHES', 'ARCHIT', 'ORCHID'))
						 || Util::StringAt($original, $current + 2, 1, array('T', 'S'))
						 || (
								(
									Util::StringAt($original, $current - 1, 1, array('A','O','U','E'))
								 || $current == 0
								)
								// e.g. 'wachtler', 'weschsler', but not 'tichner'
							 && Util::StringAt($original, $current + 2, 1, array('L','R','N','M','B','H','F','V','W',' '))
							)
						){
							$primary   .= 'K';
							$secondary .= 'K';
						} else {
							if ($current > 0){
								if (Util::StringAt($original, 0, 2, array('MC'))){
									// e.g. 'McHugh'
									$primary   .= 'K';
									$secondary .= 'K';
								} else {
									$primary   .= 'X';
									$secondary .= 'K';
								}
							} else {
								$primary   .= 'X';
								$secondary .= 'X';
							}
						}
						$current += 2;
						break;
					}

					// e.g. 'czerny'
					if (Util::StringAt($original, $current, 2, array('CZ'))
					 && !Util::StringAt($original, $current -2, 4, array('WICZ'))
					){
						$primary   .= 'S';
						$secondary .= 'X';
						$current += 2;
						break;
					}

					// e.g. 'focaccia'
					if (Util::StringAt($original, $current + 1, 3, array('CIA'))){
						$primary   .= 'X';
						$secondary .= 'X';
						$current += 3;
						break;
					}

					// double 'C', but not McClellan'
					if (Util::StringAt($original, $current, 2, array('CC'))
					 && !(
							$current == 1
						 && substr($original, 0, 1) == 'M'
						)
					){
						// 'bellocchio' but not 'bacchus'
						if (Util::StringAt($original, $current + 2, 1, array('I','E','H'))
						 && !Util::StringAt($original, $current + 2, 2, array('HU'))
						){
							// 'accident', 'accede', 'succeed'
							if ((
									$current == 1
								 && substr($original, $current - 1, 1) == 'A'
								)
							 || Util::StringAt($original, $current - 1, 5,array('UCCEE', 'UCCES'))
							){
								$primary   .= 'KS';
								$secondary .= 'KS';
								// 'bacci', 'bertucci', other italian
							} else {
								$primary   .= 'X';
								$secondary .= 'X';
							}
							$current += 3;
							break;
						} else {
							// Pierce's rule
							$primary   .= 'K';
							$secondary .= 'K';
							$current += 2;
							break;
						}
					}

					if (Util::StringAt($original, $current, 2, array('CK','CG','CQ'))){
						$primary   .= 'K';
						$secondary .= 'K';
						$current += 2;
						break;
					}

					if (Util::StringAt($original, $current, 2, array('CI','CE','CY'))){
						// italian vs. english
						if (Util::StringAt($original, $current, 3, array('CIO','CIE','CIA'))){
							$primary   .= 'S';
							$secondary .= 'X';
						} else {
							$primary   .= 'S';
							$secondary .= 'S';
						}
						$current += 2;
						break;
					}

					// else
					$primary   .= 'K';
					$secondary .= 'K';

					// name sent in 'mac caffrey', 'mac gregor'
					if (Util::StringAt($original, $current + 1, 2, array(' C',' Q',' G'))){
						$current += 3;
					} else {
						if (Util::StringAt($original, $current + 1, 1, array('C','K','Q'))
						 && !Util::StringAt($original, $current + 1, 2, array('CE','CI'))
						){
							$current += 2;
						} else {
							++$current;
						}
					}
					break;

				case 'D':
					if (Util::StringAt($original, $current, 2, array('DG'))){
						if (Util::StringAt($original, $current + 2, 1, array('I','E','Y'))){
							// e.g. 'edge'
							$primary   .= 'J';
							$secondary .= 'J';
							$current += 3;
							break;
						} else {
							// e.g. 'edgar'
							$primary   .= 'TK';
							$secondary .= 'TK';
							$current += 2;
							break;
						}
					}

					if (Util::StringAt($original, $current, 2, array('DT','DD'))){
						$primary   .= 'T';
						$secondary .= 'T';
						$current += 2;
						break;
					}

					// else
					$primary   .= 'T';
					$secondary .= 'T';
					++$current;
					break;

				case 'F':
					if (substr($original, $current + 1, 1) == 'F'){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'F';
					$secondary .= 'F';
					break;

				case 'G':
					if (substr($original, $current + 1, 1) == 'H'){
						if ($current > 0
						 && !Util::IsVowel($original, $current - 1)
						){
							$primary   .= 'K';
							$secondary .= 'K';
							$current += 2;
							break;
						}

						if ($current < 3){
							// 'ghislane', 'ghiradelli'
							if ($current == 0){
								if (substr($original, $current + 2, 1) == 'I'){
									$primary   .= 'J';
									$secondary .= 'J';
								} else {
									$primary   .= 'K';
									$secondary .= 'K';
								}
								$current += 2;
								break;
							}
						}

						// Parker's rule (with some further refinements) - e.g. 'hugh'
						if ((
								$current > 1
							 && Util::StringAt($original, $current - 2, 1, array('B','H','D'))
							)
						// e.g. 'bough'
						 || (
								$current > 2
							 && Util::StringAt($original, $current - 3, 1, array('B','H','D'))
							)
						// e.g. 'broughton'
						 || (
								$current > 3
							 && Util::StringAt($original, $current - 4, 1, array('B','H'))
							)
						){
							$current += 2;
							break;
						} else {
							// e.g. 'laugh', 'McLaughlin', 'cough', 'gough', 'rough', 'tough'
							if ($current > 2
							 && substr($original, $current - 1, 1) == 'U'
							 && Util::StringAt($original, $current - 3, 1,array('C','G','L','R','T'))
							){
								$primary   .= 'F';
								$secondary .= 'F';
							} else if (
								$current > 0
							 && substr($original, $current - 1, 1) != 'I'
							){
								$primary   .= 'K';
								$secondary .= 'K';
							}
							$current += 2;
							break;
						}
					}

					if (substr($original, $current + 1, 1) == 'N'){
						if ($current == 1
						 && Util::IsVowel($original, 0)
						 && !Util::Slavo_Germanic($original)
						){
							$primary   .= 'KN';
							$secondary .= 'N';
						} else {
							// not e.g. 'cagney'
							if (!Util::StringAt($original, $current + 2, 2, array('EY'))
							 && substr($original, $current + 1) != 'Y'
							 && !Util::Slavo_Germanic($original)
							){
								$primary   .= 'N';
								$secondary .= 'KN';
							} else {
								$primary   .= 'KN';
								$secondary .= 'KN';
							}
						}
						$current += 2;
						break;
					}

					// 'tagliaro'
					if (Util::StringAt($original, $current + 1, 2,array('LI'))
					 && !Util::Slavo_Germanic($original)
					){
						$primary   .= 'KL';
						$secondary .= 'L';
						$current += 2;
						break;
					}

					// -ges-, -gep-, -gel- at beginning
					if ($current == 0
					 && (
							substr($original, $current + 1, 1) == 'Y'
						 || Util::StringAt($original, $current + 1, 2, array('ES','EP','EB','EL','EY','IB','IL','IN','IE','EI','ER'))
						)
					){
						$primary   .= 'K';
						$secondary .= 'J';
						$current += 2;
						break;
					}

					// -ger-, -gy-
					if ((
							Util::StringAt($original, $current + 1, 2,array('ER'))
						 || substr($original, $current + 1, 1) == 'Y'
						)
					 && !Util::StringAt($original, 0, 6, array('DANGER','RANGER','MANGER'))
					 && !Util::StringAt($original, $current -1, 1, array('E', 'I'))
					 && !Util::StringAt($original, $current -1, 3, array('RGY','OGY'))
					){
						$primary   .= 'K';
						$secondary .= 'J';
						$current += 2;
						break;
					}

					// italian e.g. 'biaggi'
					if (Util::StringAt($original, $current + 1, 1, array('E','I','Y'))
					 || Util::StringAt($original, $current -1, 4, array('AGGI','OGGI'))
					){
						// obvious germanic
						if ((
								Util::StringAt($original, 0, 4, array('VAN ', 'VON '))
							 || Util::StringAt($original, 0, 3, array('SCH'))
							)
						 || Util::StringAt($original, $current + 1, 2, array('ET'))
						){
							$primary   .= 'K';
							$secondary .= 'K';
						} else {
							// always soft if french ending
							if (Util::StringAt($original, $current + 1, 4, array('IER '))){
								$primary   .= 'J';
								$secondary .= 'J';
							} else {
								$primary   .= 'J';
								$secondary .= 'K';
							}
						}
						$current += 2;
						break;
					}

					if (substr($original, $current +1, 1) == 'G'){
						$current += 2;
					} else {
						++$current;
					}

					$primary   .= 'K';
					$secondary .= 'K';
					break;

				case 'H':
					// only keep if first & before vowel or btw. 2 vowels
					if ((
							$current == 0
						 || Util::IsVowel($original, $current - 1)
						)
					  && Util::IsVowel($original, $current + 1)
					){
						$primary   .= 'H';
						$secondary .= 'H';
						$current += 2;
					} else {
						++$current;
					}
					break;

				case 'J':
					// obvious spanish, 'jose', 'san jacinto'
					if (Util::StringAt($original, $current, 4, array('JOSE'))
					 || Util::StringAt($original, 0, 4, array('SAN '))
					){
						if ((
								$current == 0
							 && substr($original, $current + 4, 1) == ' '
							)
						 || Util::StringAt($original, 0, 4, array('SAN '))
						){
							$primary   .= 'H';
							$secondary .= 'H';
						} else {
							$primary   .= 'J';
							$secondary .= 'H';
						}
						++$current;
						break;
					}

					if ($current == 0
					 && !Util::StringAt($original, $current, 4, array('JOSE'))
					){
						$primary   .= 'J';  // Yankelovich/Jankelowicz
						$secondary .= 'A';
					} else {
						// spanish pron. of .e.g. 'bajador'
						if (Util::IsVowel($original, $current - 1)
						 && !Util::Slavo_Germanic($original)
						 && (
								substr($original, $current + 1, 1) == 'A'
							 || substr($original, $current + 1, 1) == 'O'
							)
						){
							$primary   .= 'J';
							$secondary .= 'H';
						} else {
							if ($current == $last){
								$primary   .= 'J';
								// $secondary .= '';
							} else {
								if (!Util::StringAt($original, $current + 1, 1, array('L','T','K','S','N','M','B','Z'))
								 && !Util::StringAt($original, $current - 1, 1, array('S','K','L'))
								){
									$primary   .= 'J';
									$secondary .= 'J';
								}
							}
						}
					}

					if (substr($original, $current + 1, 1) == 'J'){ // it could happen
						$current += 2;
					} else {
						++$current;
					}
					break;

				case 'K':
					if (substr($original, $current + 1, 1) == 'K'){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'K';
					$secondary .= 'K';
					break;

				case 'L':
					if (substr($original, $current + 1, 1) == 'L'){
						// spanish e.g. 'cabrillo', 'gallegos'
						if ((
								$current == ($length - 3)
							 && Util::StringAt($original, $current - 1, 4, array('ILLO','ILLA','ALLE'))
							)
						 || (
								(
									Util::StringAt($original, $last-1, 2, array('AS','OS'))
								 || Util::StringAt($original, $last, 1, array('A','O'))
								)
							 && Util::StringAt($original, $current - 1, 4, array('ALLE'))
							)
						){
							$primary   .= 'L';
							// $secondary .= '';
							$current += 2;
							break;
						}
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'L';
					$secondary .= 'L';
					break;

				case 'M':
					if ((
							Util::StringAt($original, $current - 1, 3,array('UMB'))
						 && (
								($current + 1) == $last
							 || Util::StringAt($original, $current + 2, 2, array('ER'))
							)
						)
					  // 'dumb', 'thumb'
					 || substr($original, $current + 1, 1) == 'M'
					){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'M';
					$secondary .= 'M';
					break;

				case 'N':
					if (substr($original, $current + 1, 1) == 'N'){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'N';
					$secondary .= 'N';
					break;

				case 'Ñ':
					++$current;
					$primary   .= 'N';
					$secondary .= 'N';
					break;

				case 'P':
					if (substr($original, $current + 1, 1) == 'H'){
						$current += 2;
						$primary   .= 'F';
						$secondary .= 'F';
						break;
					}

					// also account for "campbell" and "raspberry"
					if (Util::StringAt($original, $current + 1, 1, array('P','B'))){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'P';
					$secondary .= 'P';
					break;

				case 'Q':
					if (substr($original, $current + 1, 1) == 'Q'){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'K';
					$secondary .= 'K';
					break;

				case 'R':
					// french e.g. 'rogier', but exclude 'hochmeier'
					if ($current == $last
					 && !Util::Slavo_Germanic($original)
					 && Util::StringAt($original, $current - 2, 2,array('IE'))
					 && !Util::StringAt($original, $current - 4, 2,array('ME','MA'))
					){
						// $primary   .= '';
						$secondary .= 'R';
					} else {
						$primary   .= 'R';
						$secondary .= 'R';
					}
					if (substr($original, $current + 1, 1) == 'R'){
						$current += 2;
					} else {
						++$current;
					}
					break;

				case 'S':
					// special cases 'island', 'isle', 'carlisle', 'carlysle'
					if (Util::StringAt($original, $current - 1, 3, array('ISL','YSL'))){
						++$current;
						break;
					}

					// special case 'sugar-'
					if ($current == 0
					 && Util::StringAt($original, $current, 5, array('SUGAR'))
					){
						$primary   .= 'X';
						$secondary .= 'S';
						++$current;
						break;
					}

					if (Util::StringAt($original, $current, 2, array('SH'))){
						// germanic
						if (Util::StringAt($original, $current + 1, 4, array('HEIM','HOEK','HOLM','HOLZ'))){
							$primary   .= 'S';
							$secondary .= 'S';
						} else {
							$primary   .= 'X';
							$secondary .= 'X';
						}
						$current += 2;
						break;
					}

					// italian & armenian 
					if (Util::StringAt($original, $current, 3, array('SIO','SIA'))
					 || Util::StringAt($original, $current, 4, array('SIAN'))
					){
						if (!Util::Slavo_Germanic($original)){
							$primary   .= 'S';
							$secondary .= 'X';
						} else {
							$primary   .= 'S';
							$secondary .= 'S';
						}
						$current += 3;
						break;
					}

					// german & anglicisations, e.g. 'smith' match 'schmidt', 'snider' match 'schneider'
					// also, -sz- in slavic language altho in hungarian it is pronounced 's'
					if ((
							$current == 0
						 && Util::StringAt($original, $current + 1, 1, array('M','N','L','W'))
						)
					 || Util::StringAt($original, $current + 1, 1, array('Z'))
					){
						$primary   .= 'S';
						$secondary .= 'X';
						if (Util::StringAt($original, $current + 1, 1, array('Z'))){
							$current += 2;
						} else {
							++$current;
						}
						break;
					}

				  if (Util::StringAt($original, $current, 2, array('SC'))){
					// Schlesinger's rule 
					if (substr($original, $current + 2, 1) == 'H')
						// dutch origin, e.g. 'school', 'schooner'
						if (Util::StringAt($original, $current + 3, 2, array('OO','ER','EN','UY','ED','EM'))){
							// 'schermerhorn', 'schenker' 
							if (Util::StringAt($original, $current + 3, 2, array('ER','EN'))){
								$primary   .= 'X';
								$secondary .= 'SK';
							} else {
								$primary   .= 'SK';
								$secondary .= 'SK';
							}
							$current += 3;
							break;
						} else {
							if ($current == 0
							 && !Util::IsVowel($original, 3)
							 && substr($original, $current + 3, 1) != 'W'
							){
								$primary   .= 'X';
								$secondary .= 'S';
							} else {
								$primary   .= 'X';
								$secondary .= 'X';
							}
							$current += 3;
							break;
						}

						if (Util::StringAt($original, $current + 2, 1,array('I','E','Y'))){
							$primary   .= 'S';
							$secondary .= 'S';
							$current += 3;
							break;
						}

						// else
						$primary   .= 'SK';
						$secondary .= 'SK';
						$current += 3;
						break;
					}

					// french e.g. 'resnais', 'artois'
					if ($current == $last
					 && Util::StringAt($original, $current - 2, 2, array('AI','OI'))
					){
						// $primary   .= '';
						$secondary .= 'S';
					} else {
						$primary   .= 'S';
						$secondary .= 'S';
					}

					if (Util::StringAt($original, $current + 1, 1, array('S','Z'))){
						$current += 2;
					} else {
						++$current;
					}
					break;

				case 'T':
					if (Util::StringAt($original, $current, 4, array('TION'))){
						$primary   .= 'X';
						$secondary .= 'X';
						$current += 3;
						break;
					}

					if (Util::StringAt($original, $current, 3, array('TIA','TCH'))){
						$primary   .= 'X';
						$secondary .= 'X';
						$current += 3;
						break;
					}

					if (Util::StringAt($original, $current, 2, array('TH'))
					 || Util::StringAt($original, $current, 3, array('TTH'))
					){
						// special case 'thomas', 'thames' or germanic
						if (Util::StringAt($original, $current + 2, 2, array('OM','AM'))
						 || Util::StringAt($original, 0, 4, array('VAN ','VON '))
						 || Util::StringAt($original, 0, 3, array('SCH'))
						){
							$primary   .= 'T';
							$secondary .= 'T';
						} else {
							$primary   .= '0';
							$secondary .= 'T';
						}
						$current += 2;
						break;
					}

					if (Util::StringAt($original, $current + 1, 1, array('T','D'))){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'T';
					$secondary .= 'T';
					break;

				case 'V':
					if (substr($original, $current + 1, 1) == 'V'){
						$current += 2;
					} else {
						++$current;
					}
					$primary   .= 'F';
					$secondary .= 'F';
					break;

				case 'W':
					// can also be in middle of word
					if (Util::StringAt($original, $current, 2, array('WR'))){
						$primary   .= 'R';
						$secondary .= 'R';
						$current += 2;
						break;
					}

					if (($current == 0)
					 && (
							Util::IsVowel($original, $current + 1)
						 || Util::StringAt($original, $current, 2, array('WH'))
						)
					){
						// Wasserman should match Vasserman 
						if (Util::IsVowel($original, $current + 1)){
							$primary   .= 'A';
							$secondary .= 'F';
						} else {
							// need Uomo to match Womo 
							$primary   .= 'A';
							$secondary .= 'A';
						}
					}

					// Arnow should match Arnoff
					if ((
							$current == $last
						&& Util::IsVowel($original, $current - 1)
						)
					 || Util::StringAt($original, $current - 1, 5, array('EWSKI','EWSKY','OWSKI','OWSKY'))
					 || Util::StringAt($original, 0, 3, array('SCH'))
					){
						// $primary   .= '';
						$secondary .= 'F';
						++$current;
						break;
					}

					// polish e.g. 'filipowicz'
					if (Util::StringAt($original, $current, 4,array('WICZ','WITZ'))){
						$primary   .= 'TS';
						$secondary .= 'FX';
						$current += 4;
						break;
					}

					// else skip it
					++$current;
					break;

				case 'X':
					// french e.g. breaux 
					if (!(
							$current == $last
						 && (
								Util::StringAt($original, $current - 3, 3, array('IAU', 'EAU'))
							 || Util::StringAt($original, $current - 2, 2, array('AU', 'OU'))
							)
						)
					){
						$primary   .= 'KS';
						$secondary .= 'KS';
					}

					if (Util::StringAt($original, $current + 1, 1, array('C','X'))){
						$current += 2;
					} else {
						++$current;
					}
					break;

				case 'Z':
					// chinese pinyin e.g. 'zhao' 
					if (substr($original, $current + 1, 1) == 'H'){
						$primary   .= 'J';
						$secondary .= 'J';
						$current += 2;
						break;

					} else if (
						Util::StringAt($original, $current + 1, 2, array('ZO', 'ZI', 'ZA'))
					 || (
							Util::Slavo_Germanic($original)
						 && (
								$current > 0
							 && substr($original, $current - 1, 1) != 'T'
							)
						)
					){
						$primary   .= 'S';
						$secondary .= 'TS';
					} else {
						$primary   .= 'S';
						$secondary .= 'S';
					}

					if (substr($original, $current + 1, 1) == 'Z'){
						$current += 2;
					} else {
						++$current;
					}
					break;

				default:
					++$current;

			} // end switch

		} // end while

		// printf("<br />ORIGINAL:   %s\n", $original);
		// printf("<br />current:    %s\n", $current);
		// printf("<br />PRIMARY:    %s\n", $primary);
		// printf("<br />SECONDARY:  %s\n", $secondary);

		$primary = substr($primary, 0, 4);
		$secondary = substr($secondary, 0, 4);

		if( $primary == $secondary ){
			$secondary = NULL;
		}

		return array(
			'primary'	=> $primary,
			'secondary'	=> $secondary
			);

	} // end of function MetaPhone

	/**
	 * Name:	StringAt($string, $start, $length, $list)
	 * Purpose:	Helper function for double_metaphone( )
	 * Return:	Bool
	 */
	public static function StringAt($string, $start, $length, $list){
		if ($start < 0
		 || $start >= strlen($string)
		){
			return 0;
		}

		foreach ($list as $t){
			if ($t == substr($string, $start, $length)){
				return 1;
			}
		}

		return 0;
	}


	/**
	 * Name:	IsVowel($string, $pos)
	 * Purpose:	Helper function for double_metaphone( )
	 * Return:	Bool
	 */
	public static function IsVowel($string, $pos){
		return preg_match("/[AEIOUY]/", substr($string, $pos, 1));
	}


	/**
	 * Name:	Slavo_Germanic($string, $pos)
	 * Purpose:	Helper function for double_metaphone( )
	 * Return:	Bool
	 */

	public static function Slavo_Germanic($string){
		return preg_match("/W|K|CZ|WITZ/", $string);
	}
}

?>
