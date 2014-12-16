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

SELECT reply_count, COUNT(p.id_network) AS post_count
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
}

?>
