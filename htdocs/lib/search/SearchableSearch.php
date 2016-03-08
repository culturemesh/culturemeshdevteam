<?php
namespace search;

class SearchableSearch extends Search {

	private $input;
	private $class;
	private $search_meta;

	public function __construct($input, $class=NULL) {

		$this->input = $input;

		if (!in_array($class, array(NULL, 'location', 'language')))
		  throw new \Exception('SearchableSearch: An invalid class was passed. For now it\'s location or language');
		else
		  $this->class = $class;

		$this->processInput();
	}

	private function processInput() {

		$input_exploded = explode(', ', $this->input);
		$this->first_term = $input_exploded[0];

		// may have to deal with commas and no spaces eg (tallahassee,florida)

		// double metaphone
		$this->search_meta = \misc\Util::DoubleMetaphone($this->first_term);
	}

	public function run($dal, $do2db) {

		//
		// create search name
		//
	
		$custom_query = $do2db->initializeCustomQuery();

		$query_name = 'customSearchableQuery' . $this->search_meta['primary'];

		$custom_query->setValues(array(
			'name' => $query_name,
			'select_rows' => array(),
			'from_tables' => array('search_keys'),
			'returning_class' => 'dobj\SearchKey'
			)
		);

		$primary_search = '%' . $this->search_meta['primary'] . '%';
		$secondary_search = NULL;

		if ($this->search_meta['secondary'] != NULL)
			$secondary_search = '%' . $this->search_meta['primary'] . '%';

		// add 
		$custom_query->addAWhere('`key`', 'LIKE', $primary_search, 's');

		if ($this->search_meta['secondary'] != NULL) {
			$custom_query->addAnotherWhere('OR', '`key`', 'LIKE', $secondary_search , 's');
		}

		if ($this->class != NULL) {

			if ($this->class == 'location') {
				$custom_query->addAnotherWhere('AND', 'class_searchable', '!=', 'language', 's');
			}

			if ($this->class == 'language') {
				$custom_query->addAnotherWhere('AND', 'class_searchable', 'NOT IN', array('city', 'region', 'country'), 's', 3);
			}
		}

		$dal->$query_name = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		// Create remora to track levenshtein distance
		$remora = new \dal\Remora();
		$remora->original_term = $this->first_term;
		$remora->levenshtein_array = array();

		$remora->setFunction(function($searchable) {

			// this is an array push, it has to be complicated
			// because of reasons
			//
			// I'm gonna simplify this thing later
			//
			$arr = $this->levenshtein_array;
			$arr[] = array(
				'name' => $searchable->name,
				'distance' => levenshtein($this->original_term, $searchable->name)
			);

			// re add the thing
			$this->levenshtein_array = $arr;
		});

		$results = $do2db->execute($dal, $custom_query->getParamObject(), $query_name, $remora);

		// if no results were found
		// create a NullResult Object
		if (get_class($results) == 'PDOStatement') {

			$results = new NullSearchResult();
			$results->setUserInput($this->input);
			$results->setAlternate($this->input);
		}
		/*
		$searchable_results = new \dobj\DObjList();

		// convert the metaphone_keys into searchables
		for ($i = 0; $i < count($results); $i++) {
			$results[$i] = $results[$i]->toSearchable();
		}
		 */

		return $results;

	}
}

?>
