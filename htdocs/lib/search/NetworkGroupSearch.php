<?php
namespace search;

/*
 * Check and see if networks can be found
 */
class NetworkGroupSearch extends Search {

	private $networks;
	private $class_to_column;

	public function __construct($networks) {

		$this->networks = $networks;
	}

	public function run($dal, $do2db) {

		// If there are no possible networks,
		// return false
		//
		if (count($this->networks) <= 0)
			return False;

		//
		// create search name
	
		$custom_query = $do2db->initializeCustomQuery();

		$custom_query->setValues(array(
			'name' => 'customNetworkGroupSearch',
			'select_rows' => array(),
			'from_tables' => array('networks'),
			'returning_class' => 'dobj\Network',
			'returning_list' => True,
			'limit_offset' => 0,
			'limit_row_count' => 4
			)
		);

		$parenthetical_conjunction = NULL;

		for ($i = 0; $i < count($this->networks); $i++) {

			$network = $this->networks[$i];

			// get their searchables
			$o = $network->origin_searchable;
			$l = $network->location_searchable;

			$origin_lines = $custom_query->createWhereLinesFromSearchable($o, 'networks', 'origin');
			$location_lines = $custom_query->createWhereLinesFromSearchable($l, 'networks', 'location', 'AND');
			$network_lines = array_merge($origin_lines, $location_lines);

			if ($i > 0) {
			  $parenthetical_conjunction = 'OR';
			}
			
			$custom_query->addAParenthetical($network_lines, $parenthetical_conjunction);
		}

		// add to dal
		$dal->customNetworkGroupSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		// still need to figure out what to do about type string
		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customNetworkGroupSearch');

		// Check for no results
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
