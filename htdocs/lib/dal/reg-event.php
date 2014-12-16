<?php

function registerEvent($obj) {

	$obj->getEventsByNetworkId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT e.*, u.id AS id_host, u.* 
FROM events e, users u 
WHERE event_date >= CURDATE() 
AND e.id_host=u.id 
AND id_network=?
ORDER BY id_network
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getEventsByNetworkId',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Event',
				'returning_cols' => array('id', 'id_network', 'id_host', 
				'date_created', 'event_date', 'title', 'address_1',
				'address_2', 'city', 'country', 'description', 'region',
				'email', 'username', 'first_name', 'last_name', 'img_link'
			)

		));
		$m->setConnection($con);
		return $m;
	};
}
