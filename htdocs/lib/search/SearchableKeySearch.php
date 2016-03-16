<?php
namespace search;

class SearchableKeySearch extends Search {

	private $input;
	private $first_term;

	private $comma_separated;

	private $param_obj;
	private $class;
	private $search_meta;

	public function __construct($input, $class=NULL) {

		$this->input = $input;

		if (!in_array($class, array(NULL, 'location', 'language')))
		  throw new \Exception('SearchableSearch: An invalid class was passed. For now it\'s location or language');
		else
		  $this->class = $class;

		$this->param_obj = new \dobj\Blank();
		$this->processInput();
	}

	private function processInput() {

		$input_exploded = explode(', ', $this->input);
		$this->first_term = $input_exploded[0];

		// may have to deal with commas and no spaces eg (tallahassee,florida)

		// double metaphone
		$this->search_meta = \misc\Util::DoubleMetaphone($this->first_term);

		// 
		// getting a bunch of comma tracking vars in here
		//
		if ($this->class === 'location') {

			$this->comma_separated = False;

			$string_count = count($input_exploded);

			// stupid structure, fix later
			if ($string_count > 1) {

				$this->comma_separated = True;
				$this->comma_string_count = $string_count;

				// take comma values starting from 2nd element
				$this->comma_values = $input_exploded;
			}
			else {
			  $this->comma_string_count = 1;
			}
		}

		/*
		for ($i = 0; $i < count($this->search_meta); $i++) {

			$key = $this->search_meta[$i];

			if ($i === 0) {
				$this->param_obj->key = array($key);
			}
			else {
				array_push($this->param_obj->key, $key);
			}
		}
		 */

		$this->param_obj->key = array($this->search_meta['primary']);

		if ($this->search_meta['secondary'] !== NULL) {
			array_push($this->param_obj->key, $this->search_meta['secondary']);
		}
	}

	public function run($dal, $do2db) {

		//
		// create search name
		//

		$key_count = count($this->param_obj->key);

		if ($key_count <= 0)
			throw new \Exception('SearchableKeySearch: Key count is invalid. Check the search parameter.');

		$query_name = NULL;

		if ($this->class == 'location') {
			if ($key_count == 1)
			  $query_name = 'LocationSingleKeySearch';
			else if ($key_count == 2)
			  $query_name = 'LocationDoubleKeySearch';
		}
		else if ($this->class == 'language') {
			if ($key_count == 1)
			  $query_name = 'LanguageSingleKeySearch';
			else if ($key_count == 2)
			  $query_name = 'LanguageDoubleKeySearch';
		}

		// Create remora to track levenshtein distance
		$remora = new \dal\Remora();
		$remora->original_term = strtolower( $this->first_term );

		$remora->setFunction(function($searchable) {

			// I'm gonna simplify this thing later
			//
			// I'm creating a search rank function here
			//
			$lowercase_term = strtolower($searchable->name);
			$searchable->lowercase_name = $lowercase_term; // for later


			$weight_distance = 1;
			$weight_population = 1;
			$weight_feature = 1;
			$weight_class = 1;

			// Calculate distance weight
			if ($lowercase_term === $this->original_term) {
				$weight_distance = 6; 
			}
			else {

				if (strpos( $lowercase_term, $this->original_term ) !== False) {
					$searchable->located_within = True;
					$weight_distance = 4;
				}

				// calculate levenshtein distance
				$distance = levenshtein($this->original_term, $lowercase_term);

				if ($distance > 0 && $distance <= 3) {
				  $weight_distance =  4 - $distance;
				}
				else 
				  $weight_distance = 1 / $distance;

				// add to searchable for later
				$searchable->distance = $distance;
			}


			// Calculate population weight
			if ($searchable->population >= 500000)
			  $weight_population = 3;
			else if ($searchable->population >= 75000)
			  $weight_population = 2;
			else
			  $weight_population = 1;
			
			// Calculate feature weight
			if ($searchable->feature_code == 'PPL')
			  $weight_feature = 1;
			else if ($searchable->feature_code == 'PPLA')
			  $weight_feature = 3;

			// calculate class weight (LOCATION ONLY)
			if (is_subclass_of($searchable, 'dobj\Location')) {

				$class = get_class($searchable);

				if ($class == 'dobj\City')
				  $weight_class = 1;
				else if ($class == 'dobj\Region')
				  $weight_class = 2;
				else if ($class == 'dobj\Country')
				  $weight_class = 3;
			}

			// Assign weight to searchable
			$weight = $weight_distance + $weight_population + $weight_feature + $weight_class;
			$searchable->search_weight = $weight;

		});

		$results = $do2db->execute($dal, $this->param_obj, $query_name, $remora);

		// if no results were found
		// create a NullResult Object
		if (get_class($results) == 'PDOStatement') {

			$results = new NullSearchResult();
			$results->setUserInput($this->input);
			$results->setAlternate($this->input);
		}
		else {
			// Sort results
			$results->sort(array('key' => 'search_weight', 'order' => 'asc'));
	  		$results = $results->slice(0, 10, True);

			$THRESHOLD = 3;

			/*
			 * I can fix this later
			 * for now, a 10 slice is good enough
			 *
			for ($i=0; $i < count($results); $i++) {
				
				if( $result[$i]->search_weight >= $THRESHOLD ) {
				  $final_results->dInsert( $result[$i] );
				}
			}
			 */

			$matches = array();

			// Process final results
			for ($i=0; $i < count($results); $i++) {

				$searchable = $results[$i];

				// reset
				$searchable->search_weight = 0;

				// string position
				//
				// 1) *** MATCH ***
				//   a) Check for multiple matches
				// 2) Located within || distance < 3
				
				$lowercase_name = strtolower($this->first_term);

				if ($lowercase_name === $searchable->lowercase_name) {

					$searchable->search_weight += 3;
					array_push($matches, $searchable);
				}
				else { 

					if ($searchable->located_within) {
					  $searchable->search_weight += 2;
					}
					else if ($searchable->distance < 3) {
					  $searchable->search_weight += (3 - $searchable->distance);
					}
				}

				$string_count = $this->comma_string_count;
				$element_count = $searchable->getElementCount();

				/*
				// if string count == 2 && elementCount == 2
				// 	// 1 : 1 string check
				//
				// if string count == 3 && elementCount == 3
				// 	// 1 : 1 string check
				//
				if ($string_count === $element_count) {

					for ($i = 1; $i < $string_count; $i++) {

						$searchable_string = strtolower( $searchable->getElement($i)['name'] );
						$user_string = strtolower( $this->comma_values[$i] );

						// add compare weight
						$searchable->search_weight += $this->calculateCompareWeight($user_string, $searchable_string);
					}
				}

				///
				// TAKING COMMAS INTO ACCOUNT
				//

				// if string count == 2 && elementCount == 3
				// 	// check 1 string against both elements

				if ($string_count < $element_count) {

					// string count WILL ALWAYS be 2
					$user_string = strtolower( $this->comma_values[1] );

					for($i = 1; $i < $element_count; $i++) {

						$searchable_string = strtolower( $searchable->getElement($i)['name'] );

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

					$searchable_string = strtolower( $searchable->getElement(1) );
						
					for($i = 1; $i < $string_count; $i++) {
						$user_string = strtolower( $this->comma_values[$i] );

						$searchable->search_weight += $this->calculateCompareWeight($user_string, $searchable_string);
					}
				}
				 */
			}

			for ($i=0; $i < count($matches); $i++) {

				$searchable = $matches[$i];

				// Boost by population, feature_code, and class
				if($searchable->population > 500000) {
				  $searchable->search_weight += 3;
				}

				if($searchable->feature_code === 'PPLA') {
				  $searchable->search_weight += 3;
				}

				// + 2 for countries, +1 for regions, no bonus for cities
				if(get_class($searchable) === 'dobj\Country') {
				  $searchable->search_weight += 2;
				}
				else if (get_class($searchable) === 'dobj\Region') {
				  $searchable->search_weight += 1;
				}
			}

			// Also check for similarities in comma values

			$results->sort(array('key' => 'search_weight', 'order' => 'asc'));
		}	

		return $results;
	}

	private function calculateCompareWeight() {

		$weight = 0;

		// check position
		//if (strpos($value['name'], $this->comma_values[$i-1]) === 0) {

		if ($s2 === $s1) {
			$weight += 4;
		}

		if (strpos($s2, $s1) === 0) {
			$weight += 3;
		}

		// check levenshtein distance
		//
		$distance = levenshtein($s1, $s2);

		if ($distance < 4) {
			$weight += 2;
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
