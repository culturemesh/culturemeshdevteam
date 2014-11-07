<?php

function registerTest($obj) {

	/*
	 * TEST
	 */
	$obj->test = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
				test id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'test',
			'params' => array('id'),
			'param_types' => 's',
			'returning' => true,
			'returning_list' => False,
			'returning_class' => 'dobj\Blank',
			'returning_cols' => array('id')
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * getUserTest
	 */
	$obj->getUserTest = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
		SELECT id, first_name
		FROM users
		WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'test',
			'params' => array('id'),
			'param_types' => 's',
			'returning' => true,
			'returning_list' => False,
			'returning_class' => 'dobj\User',
			'returning_cols' => array('id', 'first_name')
		));

		$m->setConnection($con);

		return $m;
	};
}

?>
