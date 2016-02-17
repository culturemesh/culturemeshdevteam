<?php
namespace search;

/*
 * Takes raw user input, and searches for it by name
 *
 * Analysis at Two Points
 *
 * 1) Relevance...past a certain threshold, results are not returned
 * 2) Comma Additions, parts of input
 *    that come after commas are ignored during initial search, but are
 *    considered later
 */
class SearchableByName extends Search {

	protected $search_value;
	protected $search_class;

	protected $comma_separated;
	protected $comma_string_count;
	protected $comma_values;

	public function __construct($input_value, $search_class) {

		if ($search_class == NULL) {
		  $this->search_class = 'location';
		}
		else {
		  $this->search_class = $search_class;
		}

		$this->input_value = $this->processInput($input_value);
	}

	private function processInput($raw_input) {

		$processed_input = NULL;

		// replace ', ' with ','
		$input = str_replace(', ', ',', $raw_input);
		$input_parts = explode(',', $input);
		$processed_input = '%' . $input_parts[0] . '%';

		// do both things, decide which I like later
		if ($this->search_class === 'location') {

			$this->comma_separated = False;

			$string_count = count($input_parts);

			if ($string_count > 1) {

				$this->comma_separated = True;
				$this->comma_string_count = $string_count;

				// take comma values starting from 2nd element
				$this->comma_values = $input_parts;
			}
		}

		return $processed_input;

	}

	public function run($dal, $do2db) {

		$results = NULL;
		$param_obj = new \dobj\Blank();
		$param_obj->name = $this->input_value;

		// Create remora to track levenshtein distance
		$remora = new \dal\Remora();
		$remora->input_value = $this->input_value;

		$remora->setFunction(function($searchable) {

			///
			// SET AN ADDITIONAL CRITERIA
			//
			// If name starts @ Beginning, weight it higher
			//

			// I'm gonna simplify this thing later
			//
			// I'm creating a search rank function here
			//
			// $distance = levenshtein($this->input_value, $searchable->name);

			// COMMON WEIGHTS
			$weight_searchable = 1;
			$weight_position = 1;

			// Weight if the input starts at the beginning
			if ($this->input_value === $searchable->name) {
			  $weight_position = 7;
			}
			else if (strpos($this->input_value, strtolower($searchable->name)) === 0) {
			  $weight_position = 4;
			}

			// calculate class weight (LOCATION ONLY)
			if (is_subclass_of($searchable, 'dobj\Location')) {

				$weight_population = 1;
				$weight_feature = 1;
				$weight_class = 1;

				// Calculate population weight
				if ($searchable->population >= 500000)
				  $weight_population = 6;
				else
				  $weight_population = 1;
				
				// Calculate feature weight
				if ($searchable->feature_code == 'PPL')
				  $weight_feature = 1;
				else if ($searchable->feature_code == 'PPLA')
				  $weight_feature = 3;

				$class = get_class($searchable);

				if ($class == 'dobj\City')
				  $weight_class = 1;
				else if ($class == 'dobj\Region')
				  $weight_class = 3;
				else if ($class == 'dobj\Country')
				  $weight_class = 8;
				
				$weight_searchable = $weight_population + $weight_feature + $weight_class;
			}

			if (get_class($searchable) === 'dobj\Language') {

				$weight_speakers = 1;

				$num_speakers = $searchable->num_speakers;

				// Give preference to languages with more speakers
				//
				//...
				if ($num_speakers > 100) {
				  $weight_speakers = 6;
				}
				else if ($num_speakers > 50) {
				  $weight_speakers = 5;
				}
				else if ($num_speakers > 25) {
				 $weight_speakers = 4;
				}
				else if ($num_speakers > 15) {
				 $weight_speakers = 3;
				}
				else if ($num_speakers > 10) {
				 $weight_speakers = 2;
				}

				$weight_searchable = $weight_speakers;
			}

			// Assign weight to searchable
			$weight = $weight_searchable + $weight_position;
			$searchable->search_weight = $weight;
		});

		if ($this->search_class == 'location') {
		  $query_name = 'getLocationsByName';
		}

		if ($this->search_class == 'language') {
		  $query_name = 'getLanguagesByName';
		}

		$results = $do2db->execute($dal, $param_obj, $query_name, $remora);

		if (get_class($results) == 'PDOStatement')
		  $results = False;
		else {

			// include values by relevance
			//
			// later will use sort only after determining values have passed
			// a certain threshold
	  		$results->sort(array('key' => 'search_weight', 'order' => 'asc'));
	  		$results = $results->slice(0, 10, True);

			// If there are extra comma values (denoting region/country data), poke through
			// the remaining values for matches/resemblance/relevance
			//
			// Note: This only happens for location
			//
			if ($this->search_class === 'location') {

				if ($this->comma_separated) {

					// Note: comma string count will always be
					// greater than 1

					// string count is gonna be 2 or 3
					//
					// set offset as 1, for first value
					$element_offset = 1;

					$final_results = new \dobj\DObjList();

					// We're gonna add to each of the searchable's weight
					// and the re-sort the list
					//
					// I guess
					foreach ($results as $searchable) {

						// I could reset search weight to 0...
						$searchable->search_weight = 0;

						$searchable_value; // the value that HOPEFULLY corresponds

						$string_count = $this->comma_string_count;
						$element_count = $searchable->getElementCount();

						// if string count == 2 && elementCount == 2
						// 	// 1 : 1 string check
						//
						// if string count == 3 && elementCount == 3
						// 	// 1 : 1 string check
						//
						if ($string_count === $element_count) {

							for ($i = 1; $i < $string_count; $i++) {

								$searchable_string = $searchable->getElement($i)['name'];
								$user_string = $this->comma_values[$i];

								// add compare weight
								$searchable->search_weight += $this->calculateCompareWeight($user_string, $searchable_string);
							}
						}

						// if string count == 2 && elementCount == 3
						// 	// check 1 string against both elements

						if ($string_count < $element_count) {

							// string count WILL ALWAYS be 2
							$user_string = $this->comma_values[1];

							for($i = 1; $i < $element_count; $i++) {

								$searchable_string = $searchable->getElement($i)['name'];

								// add compare weight
								$searchable->search_weight += $this->calculateCompareWeight($user_string, $searchable_string);
							}
						}

						// if string count == 2 && elementCount == 1
						// 	// skip
						//
						// if string count == 3 && elementCount == 1
						// 	// skip
						//
						// if string count == 3 && elementCount == 2
						// 	// check element against both strings
						//

						if ($string_count > $element_count && $element_count == 2) {

							$searchable_string = $searchable->getElement(1);
								
							for($i = 1; $i < $string_count; $i++) {
								$user_string = $this->comma_values[$i];

								$searchable->search_weight += $this->calculateCompareWeight($user_string, $searchable_string);
							}
						}

						// add past certain threshhold
						if ($searchable->search_weight >= 5) {
							$final_results->dInsert($searchable);
						}
					}
					// endloop
					//
	  				$final_results->sort(array('key' => 'search_weight', 'order' => 'asc'));
	  				//$results->sort(array('key' => 'search_weight', 'order' => 'asc'));
					$results = $final_results;
				}
			}
		}

		return $results;
	}

	/*
	 * S2 should be the "complete string" (ie haystack) to be checked against
	 * as in strpos
	 */
	private function calculateCompareWeight($s1, $s2) {

		$weight = 0;

		// check position
		//if (strpos($value['name'], $this->comma_values[$i-1]) === 0) {
		if (strpos($s2, $s1) === 0) {
			$weight += 10;
		}

		if ($s2 === $s1) {
			$weight += 10;
		}

		// check levenshtein distance
		//
		$distance = levenshtein($s1, $s2);

		if ($distance < 4) {
			$weight += 5;
		}

		/*
		var_dump($s1);
		var_dump($s2);
		var_dump($weight);
		echo '------';
		 */

		return $weight;
	}
}

?>
