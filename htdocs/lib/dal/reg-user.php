<?php

function registerUser($obj) {

	/*
	 * TEST
	 */
	$obj->getUserById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM users
WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getUserById',
			'params' => array('id'),
			'param_types' => 's',
			'returning' => true,
			'returning_list' => False,
			'returning_class' => 'dobj\User',
			'returning_cols' => array('id')
		));

		$m->setConnection($con);

		return $m;
	};
}

?>
