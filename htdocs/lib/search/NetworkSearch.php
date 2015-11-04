<?php
namespace search;

class NetworkSearch extends Search {

	private $searchables;

	public function __construct($state='searchable') {

		// initialize states
		$this->states = array('searchable', 'network');
		$this->current_state = $state;

		$this->processGetVariables();
	}

	private function processGetVariables() {

		// get the GET values
		$search1_value = $_GET['search-1'];
		$search2_value = $_GET['search-2'];

		$search1_array = misc\Util::DoubleMetaphone($search1_value);
		$search2_array = misc\Util::DoubleMetaphone($search2_value);
	}

	public function run($dal, $do2db) {

		//
		// create search name
	
		$custom_query = $do2db->initializeCustomQuery();

		$custom_query->setValues(array(
			'name' => 'NetworkSearchQuery',
			'select_rows' => array(),
			'from_tables' => array('networks'),
			'returning_class' => 'dobj\Network'
			)
		);

		$custom_query->addAWhere($searchables[0]->getNetworkSearchColumn(), '=', $searchables[0]->getLowestScopeId(), 'i');
		$custom_query->addAnotherWhere('AND', $searchables[0]->getNetworkSearchColumn(), '=', $searchables[0]->getLowestScopeId(), 'i');

		$dal->customNetworkSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customNetworkSearch');

		// Check for no results
		if (get_class($results) != 'PDOStatement') {

		}

		return $results;
	}
}

?>
