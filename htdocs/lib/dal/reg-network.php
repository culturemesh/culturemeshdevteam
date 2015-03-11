<?php

function registerNetwork($obj) {

	$obj->getNetworkById = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM networks
WHERE id=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNetworkById',
			'params' => array('id'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_list' => False,
			'returning_class' => 'dobj\Network',
			'returning_cols' => array('id', 'id_city_cur', 'city_cur', 'id_region_cur',
					'region_cur' , 'id_country_cur', 'country_cur', 'id_city_origin',
					'city_origin', 'id_region_origin', 'region_origin',
					'id_country_origin', 'country_origin', 'id_language_origin',
					'language_origin', 'network_class', 'date_added'
		 		)
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->getMemberCount = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT COUNT(id_network) as member_count 
FROM network_registration 
WHERE id_network=?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getMemberCount',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => true,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => NULL,
			'returning_cols' => array('member_count')
		 	)
		);

		$m->setConnection($con);

		return $m;
	};

	$obj->getPostCount = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT (IFNULL(reply_count, 0) + COUNT(p.id_network)) AS post_count
FROM posts p
LEFT JOIN (SELECT id_network, COUNT(id_network) AS reply_count
		FROM post_replies
		GROUP BY id_network) pr
ON p.id_network = pr.id_network
WHERE p.id_network=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getPostCount',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => true,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => NULL,
			'returning_cols' => array('post_count')
		 	)
		);

		$m->setConnection($con);

		return $m;
	};

	$obj->getNetworksByIds = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT *
FROM networks
WHERE id IN ? 
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNetworksByIds',
			'params' => array('idlist'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_list' => True,
			'returning_class' => 'dobj\Network',
			'returning_cols' => array('id', 'id_city_cur', 'city_cur', 'id_region_cur',
					'region_cur' , 'id_country_cur', 'country_cur', 'id_city_origin',
					'city_origin', 'id_region_origin', 'region_origin',
					'id_country_origin', 'country_origin', 'id_language_origin',
					'language_origin', 'network_class', 'date_added'
		 		)
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->getNetworksByUserId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT n.*, mc.member_count, pc.post_count, nr.join_date
			FROM networks n
			JOIN ( SELECT id_network, join_date 
				   FROM network_registration
				   WHERE id_user =?) nr
			ON nr.id_network = n.id
			LEFT JOIN ( SELECT id_network, COUNT(id_network) AS member_count
				   FROM network_registration
				   GROUP BY id_network
				   ORDER BY member_count) mc
			ON mc.id_network=nr.id_network AND mc.id_network=n.id
			LEFT JOIN (SELECT id_network, COUNT(id_network) AS post_count
				  FROM posts
				  GROUP BY id_network
				  ORDER BY post_count) pc
				  ON pc.id_network=nr.id_network 
				  AND pc.id_network=mc.id_network 
				  AND pc.id_network=n.id
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNetworksByUserId',
			'params' => array('id_user'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_list' => True,
			'returning_class' => 'dobj\Network',
			'returning_cols' => array('id', 'id_city_cur', 'city_cur', 'id_region_cur',
					'region_cur' , 'id_country_cur', 'country_cur', 'id_city_origin',
					'city_origin', 'id_region_origin', 'region_origin',
					'id_country_origin', 'country_origin', 'id_language_origin',
					'language_origin', 'network_class', 'date_added',
					'member_count', 'post_count', 'join_date'
		 		)
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->checkUserRegistration = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT COUNT(id_user) AS user_count 
FROM network_registration 
WHERE id_user=? 
AND id_network=?

SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNetworksByUserId',
			'params' => array('id_user', 'id_network'),
			'param_types' => 'ii',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_list' => False,
			'returning_class' => 'dobj\Blank',
			'returning_cols' => array('user_count')
		));

		$m->setConnection($con);

		return $m;
	};
}

?>
