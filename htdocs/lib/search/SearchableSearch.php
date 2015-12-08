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

		// may have to deal with commas and no spaces eg (tallahassee,florida)

		// double metaphone
		$this->search_meta = \misc\Util::DoubleMetaphone($input_exploded[0]);
	}

	public function run($dal, $do2db) {

		//
		// create search name
	
		$custom_query = $do2db->initializeCustomQuery();

		$query_name = 'customSearchableQuery' . $this->search_meta['primary'];

		$custom_query->setValues(array(
			'name' => $query_name,
			'select_rows' => array(),
			'from_tables' => array('metaphone_keys'),
			'returning_class' => 'dobj\MetaphoneKey'
			)
		);

		$primary_search = '%' . $this->search_meta['primary'] . '%';
		$secondary_search = NULL;

		if ($this->search_meta['secondary'] != NULL)
			$secondary_search = '%' . $this->search_meta['primary'] . '%';

		// add 
		$custom_query->addAWhere('meta_key', 'LIKE', $primary_search, 's');

		if ($this->search_meta['secondary'] != NULL) {
			$custom_query->addAnotherWhere('OR', 'meta_key', 'LIKE', $secondary_search , 's');
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

		$results = $do2db->execute($dal, $custom_query->getParamObject(), $query_name);

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
