<?php

function registerNetwork($obj) {

	$obj->getNetworkById = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM networks n
LEFT JOIN (SELECT * FROM
	network_tweet_query_data) ntqs
ON n.id = ntqs.id_network
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
					'language_origin', 'network_class', 'date_added',
					'query_origin_scope', 'query_location_scope', 'query_level', 'query_since_date',
					'query_auto_update', 'query_default', 'query_custom', 'tweet_count'
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
SELECT (IFNULL(tweet_count, 0) + IFNULL(tweet_reply_count, 0) + IFNULL(reply_count, 0) + COUNT(p.id_network)) AS post_count
FROM posts p
LEFT JOIN (SELECT id_network, COUNT(id_network) AS reply_count
		FROM post_replies
		GROUP BY id_network) pr
ON p.id_network = pr.id_network
LEFT JOIN (SELECT id_network, COUNT(id_network) AS tweet_count
		FROM post_tweets
		GROUP BY id_network) pt
ON p.id_network = pt.id_network
LEFT JOIN (SELECT id_network, COUNT(id_network) AS tweet_reply_count
		FROM post_tweet_replies
		GROUP BY id_network) ptr
ON p.id_network = ptr.id_network
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

	$obj->insertQueryRow = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

INSERT INTO network_tweet_query_data
(id_network) VALUES
(?)
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'insertQueryRow',
			'params' => array('id_network'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_list' => False,
			'returning_class' => NULL,
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

$obj->addUserToNetwork = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

INSERT INTO network_registration
(id_user, id_network, join_date) VALUES
(?, ?, NOW())
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'addUserToNetwork',
			'params' => array('id_user', 'id_network'),
			'param_types' => 'ii',
			'nullable' => array(),
			'returning' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_list' => False,
			'returning_class' => NULL,
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

$obj->getTopFourNetworks = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT n.id, n.city_cur, n.region_cur, n.country_cur, n.city_origin, n.region_origin, n.country_origin, n.language_origin, n.network_class, nr.member_count, p.post_count
FROM networks n 
JOIN (SELECT id_network, COUNT(id_network) AS member_count
	    FROM network_registration
	    GROUP BY id_network
	    ORDER BY member_count DESC) nr  
ON n.id = nr.id_network
LEFT JOIN (SELECT p.id_network, COUNT(p.id_network) + IFNULL(pr.reply_count,0) AS post_count
	FROM posts p
	LEFT JOIN (SELECT id_network, COUNT(id_network) AS reply_count
		FROM post_replies
		GROUP BY id_network) pr
	ON p.id_network=pr.id_network
	GROUP BY id_network) p
ON n.id = p.id_network AND nr.id_network = p.id_network
GROUP BY n.id
ORDER BY nr.member_count DESC, p.post_count DESC
LIMIT 0,4
SQL
		/////////////////////////////
		,	'name' => 'getTopFourNetworks',
			'returning' => True,
			'returning_list' => True,
			'returning_class' => 'dobj\Network',
		));

		$m->setConnection($con);

		return $m;
	};
}
?>
