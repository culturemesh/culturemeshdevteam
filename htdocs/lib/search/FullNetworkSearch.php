<?php
namespace search;

class FullNetworkSearch extends Search {

	private $states;
	private $input;

	// 1st state search items
	private $origin_search;
	private $location_search;

	private $origin_searchable;
	private $location_searchable;

	// 2nd state search items
	private $network_search;
	private $related_search;

	public function __construct($input, $state='searchable') {

		// input
		$this->input = $input;

		// initialize states
		$this->states = array('searchable', 'network');

		if ($input['click_1'] == 1 && $input['click_2'] == 1) {
			$state = 'network';
		}

		$this->current_state = $state;

		// something
		if ($this->current_state == 'searchable') {

			// create searches
			$search_class = NULL;

			if ($this->input['verb'] == 'arefrom')
			  $search_class = 'location';
			if ($this->input['verb'] == 'speak')
			  $search_class = 'language';

			// origin search
			$this->origin_search = new SearchableKeySearch($this->input['search_one'], $search_class);

			// location search
			$this->location_search = new SearchableKeySearch($this->input['search_two'], 'location');
		}
		else if ($this->current_state = 'network') {

			$this->network_search = new NetworkSearch($this->input['origin_searchable'], $this->input['location_searchable']);
			$this->related_search = new RelatedNetworkSearch($this->input['origin_searchable'], $this->input['location_searchable']);
		}
	}

	public function run($dal, $do2db) {

		$results = array();

		if ($this->current_state == 'searchable') {

			$results['origin'] = $this->origin_search->run($dal, $do2db);
			$results['location'] = $this->location_search->run($dal, $do2db);

			$this->current_state = 'network';
		}
		else if ($this->current_state == 'network') {

			$results['main_network'] = $this->network_search->run($dal, $do2db);
			$results['related_networks'] = $this->related_search->run($dal, $do2db);
		}

		return $results;
	}
}

?>
