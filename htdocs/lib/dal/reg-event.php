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

	$obj->getEventsInYourNetworks = function($con=NULL) {
	
		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT e.*, n.network_class, 
n.city_cur, n.region_cur, n.country_cur,
n.city_origin, n.region_origin, n.country_origin,
n.language_origin,  u.img_link AS usr_image
FROM events e, users u, networks n
WHERE id_network IN (
SELECT id_network
FROM network_registration
WHERE id_user=?)
AND e.id_host = u.id
AND n.id = e.id_network
ORDER BY e.id_network
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getEventsInYourNetworks',
			'params' => array('id_user'),
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
				'network_class', 'city_cur', 'region_cur', 'country_cur',
				'city_origin', 'region_origin', 'country_origin', 'language_origin',
				'usr_image'
			)
		));

		$m->setConnection($con);
		return $m;
	};
	$obj->getEventsByUserId = function($con=NULL) {
	
		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT e.*, u.first_name, u.last_name, u.img_link
FROM events e, users u 
WHERE e.id_host=u.id 
AND id_host=?
ORDER BY e.id_network
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getEventsByUserId',
			'params' => array('id_host'),
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

	$obj->getEventRegistrationsByUserId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT er.*, e.*, u.img_link, u.first_name, u.last_name
FROM event_registration er, events e, users u
WHERE er.id_event = e.id 
AND e.id_host = u.id
AND er.id_guest=?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getEventsInYourNetworks',
			'params' => array('id_guest'),
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
