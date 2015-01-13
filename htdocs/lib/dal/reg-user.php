<?php

function registerUser($obj) {

	/*
	 * TEST
	 */
	$obj->getUserById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			/*
			'query' => <<<SQL
SELECT *
FROM users
WHERE id=?
SQL
			 */
			'query' => <<<SQL
SELECT u.*, GROUP_CONCAT(er.id_event SEPARATOR ', ') AS events_attending
FROM users u, event_registration er
WHERE u.id=?
AND u.id=er.id_guest
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getUserById',
			'params' => array('id'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => 'dobj\User',
			'returning_cols' => array('id', 'events_attending')
		));

		$m->setConnection($con);

		return $m;
	};
}

?>
