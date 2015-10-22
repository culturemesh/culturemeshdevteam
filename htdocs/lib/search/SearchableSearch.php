<?php
namespace search;

class SearchableSearch extends Search {

	private $input;
	private $search_meta;

	public function __construct($input) {

		$this->input = $input;
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

		// add 
		$custom_query->addAWhere('meta_key', 'LIKE', $this->search_meta['primary'], 's');

		if ($this->search_meta['secondary'] != NULL) {
			$custom_query->addAnotherWhere('OR', 'meta_key', 'LIKE', $this->search_meta['secondary'] , 's');
		}

		$dal->$query_name = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), $query_name);

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
