<?php

function registerMeta($obj) {

	/*
	 * Inserts a new post
	 *
	 */
	$obj->selectFoundRows = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT FOUND_ROWS() as found_rows
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'selectFoundRows',
			'params' => NULL,
			'param_types' => NULL,
			'nullable' => NULL,
			'returning' => true,
			'returning_list' => false,
			'returning_value' => True,
			'returning_assoc' => false,
			'returning_class' => null,
			'returning_cols' => array('found_rows')
		));
		$m->setConnection($con);
		return $m;
	};
}

?>
