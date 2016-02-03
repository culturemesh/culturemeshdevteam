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
			$distance = levenshtein($this->input_value, $searchable->name);

			$weight_distance = 1;
			$weight_population = 1;
			$weight_feature = 1;
			$weight_class = 1;

			// Calculate distance weight
			if ($distance === 0)
			  $weight_distance = 5;
			else if ($distance > 0 && $distance < 3) {
			  $weight_distance = $distance;
			}
			else if (strpos( $searchable->name, $this->input_value ) !== False)
			  $weight_distance = 4;
			else {
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

		if ($this->search_class == 'location') {
		  $query_name = 'getLocationsByName';
		}

		if ($this->search_class == 'language') {
		  $query_name = 'getLanguagesByName';
		}

		$results = $do2db->execute($dal, $param_obj, $query_name, NULL);
		$results->sort(array('key' => 'search_weight', 'order' => 'asc'));

		return $results->slice(0, 5, True); 
	}
}

?>
