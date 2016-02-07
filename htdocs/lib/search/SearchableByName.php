<?php
namespace search;

class SearchableByName extends Search {

	protected $input_value;
	protected $search_class;

	public function __construct($input_value, $search_class) {

		$this->input_value = '%' . strtolower($input_value) . '%';

		if ($search_class == NULL) {
		  $this->search_class = 'location';
		}
		else {
		  $this->search_class = $search_class;
		}
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
		  $results->sort(array('key' => 'search_weight', 'order' => 'asc'));
		  $results = $results->slice(0, 5, True);
		}

		return $results;
	}
}

?>
