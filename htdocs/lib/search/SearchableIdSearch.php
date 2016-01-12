<?php
namespace search;

class SearchableIdSearch extends Search {

	private $search_id;
	private $search_class;

	public function __construct($id, $class) {

		$this->search_id = (int) $id;
		$this->search_class = $class;
	}

	public function run($dal, $do2db) {

		$query_name = NULL;

		// Assigns query name so that we know what to
		// pass to do2db
		if (in_array($this->search_class, array('dobj\City', '\dobj\City'))) {
		  $query_name = 'getCityById';
		}
		else if (in_array($this->search_class, array('dobj\Region', '\dobj\Region'))) {
		  $query_name = 'getRegionById';
		}
		else if (in_array($this->search_class, array('dobj\Country', '\dobj\Country'))) {
		  $query_name = 'getCountryById';
		}
		else if (in_array($this->search_class, array('dobj\Language', '\dobj\Language'))) {
		  $query_name = 'getLanguageById';
		}

		$param_obj = new \dobj\Blank();
		$param_obj->id = $this->search_id;

		$results = $do2db->execute($dal, $param_obj, $query_name);

		// if no results were found
		// create a NullResult Object
		if (get_class($results) == 'PDOStatement') {

			return False;
		}

		return $results;
	}
}

?>
