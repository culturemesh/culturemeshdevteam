<?php

function registerUser($obj) {

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getUserById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL

SELECT u.*, 
GROUP_CONCAT(nr.id_network SEPARATOR ', ') AS network_membership,
GROUP_CONCAT(DISTINCT er.id_event SEPARATOR ', ') AS events_attending
FROM users u
LEFT JOIN ( SELECT id_user, id_network FROM network_registration) nr ON u.id= nr. id_user
LEFT JOIN ( SELECT id_guest, id_event FROM event_registration) er ON u.id= er. id_guest
WHERE u.id=?
GROUP BY u.id
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getUserById',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => 'dobj\User',
			'returning_cols' => array('id', 'events_attending', 'network_membership')
		));

		$m->setConnection($con);

		return $m;
	};
}

?>
