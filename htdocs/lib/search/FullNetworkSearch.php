<?php
namespace search;

class FullNetworkSearch extends Search {

	private $states;
	private $input;

	// 2nd state search items
	private $s2_search;

	public function __construct($input, $state='searchable') {

		// input
		$this->input = $input;

		// initialize states
		$this->states = array('searchable', 'network');
		$this->current_state = $state;

		// something
		if ($state == 'searchable') {

			// create searches
			$this->s1_searches = array();
			array_push($this->s1_searches, new SearchableSearch($this->input['search-1']));
			array_push($this->s1_searches, new SearchableSearch($this->input['search-2']));
		}
	}

	public function run($dal, $do2db) {

		$results = array();

		if ($this->current_state == 'searchable') {

			foreach($this->s1_searches as $search) {
				array_push($results, $search->run($dal, $do2db));
			}

			$this->current_state = 'network';
		}
		else if ($this->current_state == 'network') {
			array_push($results, $this->s2_search->run($dal, $do2db));
		}

		return $results;
	}
}

?>
