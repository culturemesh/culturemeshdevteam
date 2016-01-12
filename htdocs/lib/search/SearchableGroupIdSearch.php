<?php
namespace search;

class SearchableGroupIdSearch extends Search {

	private $search_id;
	private $search_class;

	private $c_to_c;

	public function __construct($ids, $class) {

		$this->search_ids = $ids;
		$this->search_class = $class;

		$this->c_to_c = array(
			'dobj\City' => array(
				'table' => 'cities'),
			'dobj\Region' => array(
				'table' => 'regions'),
			'dobj\Country' => array(
				'table' => 'countries'),
			'dobj\Language' => array(
				'table' => 'languages'));
	}

	public function run($dal, $do2db) {

		$c_to_c = $this->c_to_c[ $this->search_class ];

		$custom_query = $do2db->initializeCustomQuery();
		$custom_query->setValues(array(
			'name' => 'customGroupSearchableSearch',
			'select_rows' => array(),
			'from_tables' => array( $c_to_c['table'] ),
			'returning_class' => $this->search_class,
			'returning_list' => True
			)
		);

		$type_string = '';

		for ($i = 0; $i < count($this->search_ids); $i++) {
			$type_string .= 'i';
		}

		// Add statement to the query
		$custom_query->addAWhere('id', 'IN', $this->search_ids, $type_string, count($this->search_ids));


		$dal->customGroupSearchableSearch = function($con=NULL) use ($custom_query) {
			return $custom_query->toDBQuery($con);
		};

		$results = $do2db->execute($dal, $custom_query->getParamObject(), 'customGroupSearchableSearch');

		// if no results were found
		// create a NullResult Object
		if (get_class($results) == 'PDOStatement') {
			return False;
		}

		return $results;
	}
}

?>
