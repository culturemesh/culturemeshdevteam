<?php
namespace search;

class SearchableKeySearch extends Search {

	private $input;
	private $first_term;

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


			$weight_distance = 1;
			$weight_population = 1;
			$weight_feature = 1;
			$weight_class = 1;

			// Calculate distance weight
			if ($lowercase_term === $this->original_term) {
				$weight_distance = 12;
			}
			else {

				// calculate levenshtein distance
				$distance = levenshtein($this->original_term, $lowercase_term);

				if ($distance === 0)
				  $weight_distance = 5;
				else if ($distance > 0 && $distance < 3) {
				  $weight_distance = $distance;
				}
				else if (strpos( $lowercase_term, $this->original_term ) !== False)
				  $weight_distance = 4;
				else 
				  $weight_distance = 1 / $distance;
			}


			// Calculate population weight
			if ($searchable->population >= 500000)
			  $weight_population = 13;
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
		}	

		return $results;
	}
}

?>
