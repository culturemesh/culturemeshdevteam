<?php

function registerPost($obj) {

	$obj->getPostById = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT p.*, u.email, u.username, u.first_name, u.last_name, u.img_link, hash
FROM posts p
JOIN (SELECT *
	FROM users) u ON p.id_user = u.id
LEFT JOIN ( SELECT id_post, GROUP_CONCAT(hash SEPARATOR ', ') AS hash
		FROM images i
		LEFT JOIN post_images pi ON pi.id_image1 = i.id
					OR pi.id_image2 = i.id
					OR pi.id_image3 = i.id
                GROUP BY id_post
		) hh 
ON p.id = hh.id_post
WHERE p.id=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getPostById',
			'params' => array('id'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Post',
			'returning_cols' => array('id', 'id_user', 'id_network', 
				'post_date', 'post_text', 'post_class', 
				'post_original', 'email', 'username', 
				'first_name', 'last_name', 'img_link', 
				'hash')

));
		$m->setConnection($con);
		return $m;
	};

	$obj->getPostsByNetworkId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT SQL_CALC_FOUND_ROWS p.*, u.email, u.username, u.first_name, u.last_name, u.img_link, reply_count, hash
FROM posts p
LEFT JOIN (SELECT id_parent, COUNT(id_parent) AS reply_count
		FROM post_replies
		GROUP BY id_parent) pr
ON p.id = pr.id_parent
JOIN (SELECT *
	FROM users) u ON p.id_user = u.id
LEFT JOIN ( SELECT id_post, GROUP_CONCAT(hash SEPARATOR ', ') AS hash
		FROM images i
		LEFT JOIN post_images pi ON pi.id_image1 = i.id
					OR pi.id_image2 = i.id
					OR pi.id_image3 = i.id
                GROUP BY id_post
		) hh 
ON p.id = hh.id_post
WHERE p.id_network=?
ORDER BY post_date DESC
LIMIT ?, ?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getPostsByNetworkId',
			'params' => array('id_network', 'lobound', 'upbound'),
			'param_types' => 'sii',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Post',
			'returning_cols' => array('id', 'id_user', 'id_network', 
				'post_date', 'post_text', 'post_class', 
				'post_original', 'email', 'username', 
				'first_name', 'last_name', 'img_link', 
				'reply_count', 'hash')

));
		$m->setConnection($con);
		return $m;
	};

	$obj->getOlderPostsFromId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT SQL_CALC_FOUND_ROWS p.*, u.email, u.username, u.first_name, u.last_name, u.img_link, reply_count, hash
FROM posts p
LEFT JOIN (SELECT id_parent, COUNT(id_parent) AS reply_count
		FROM post_replies
		GROUP BY id_parent) pr
ON p.id = pr.id_parent
JOIN (SELECT *
	FROM users) u ON p.id_user = u.id
LEFT JOIN ( SELECT id_post, GROUP_CONCAT(hash SEPARATOR ', ') AS hash
		FROM images i
		LEFT JOIN post_images pi ON pi.id_image1 = i.id
					OR pi.id_image2 = i.id
					OR pi.id_image3 = i.id
                GROUP BY id_post
		) hh 
ON p.id = hh.id_post
WHERE p.id_network=?
AND p.id <= ?
ORDER BY post_date DESC
LIMIT ?, ?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getOlderPostsFromId',
			'params' => array('id_network', 'id', 'lobound', 'upbound'),
			'param_types' => 'siii',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Post',
			'returning_cols' => array('id', 'id_user', 'id_network', 
				'post_date', 'post_text', 'post_class', 
				'post_original', 'email', 'username', 
				'first_name', 'last_name', 'img_link', 
				'reply_count', 'hash')

));
		$m->setConnection($con);
		return $m;
	};

	$obj->getNewerPostsFromId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
SELECT SQL_CALC_FOUND_ROWS p.*, u.email, u.username, u.first_name, u.last_name, u.img_link, reply_count, hash
FROM posts p
LEFT JOIN (SELECT id_parent, COUNT(id_parent) AS reply_count
		FROM post_replies
		GROUP BY id_parent) pr
ON p.id = pr.id_parent
JOIN (SELECT *
	FROM users) u ON p.id_user = u.id
LEFT JOIN ( SELECT id_post, GROUP_CONCAT(hash SEPARATOR ', ') AS hash
		FROM images i
		LEFT JOIN post_images pi ON pi.id_image1 = i.id
					OR pi.id_image2 = i.id
					OR pi.id_image3 = i.id
                GROUP BY id_post
		) hh 
ON p.id = hh.id_post
WHERE p.id_network=?
AND p.id >= ?
ORDER BY post_date ASC
LIMIT 0, 10
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNewerPostsFromId',
			'params' => array('id_network', 'id', 'lobound', 'upbound'),
			'param_types' => 'siii',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Post',
			'returning_cols' => array('id', 'id_user', 'id_network', 
				'post_date', 'post_text', 'post_class', 
				'post_original', 'email', 'username', 
				'first_name', 'last_name', 'img_link', 
				'reply_count', 'hash')

));
		$m->setConnection($con);
		return $m;
	};
	$obj->getRepliesByParentId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT p.*, u.email, u.username, u.first_name, u.last_name, u.img_link
FROM post_replies p, users u
WHERE p.id_user=u.id
AND p.id_parent=?
ORDER BY reply_date DESC
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getRepliesByParentId',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Reply',
			'returning_cols' => array('id', 'id_parent', 'id_user', 'id_network', 
				'reply_date', 'reply_text', 'email', 'username', 
				'first_name', 'last_name', 'img_link'
			)

		));
		$m->setConnection($con);
		return $m;
	};

	$obj->getPostsByUserId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT p.*, u.first_name, u.last_name, u.img_link,
n.network_class, n.city_cur, n.region_cur, n.country_cur,
n.city_origin, n.region_origin, n.country_origin, n.language_origin
FROM posts p, users u, networks n
WHERE p.id_user=u.id 
AND p.id_network = n.id
AND p.post_text <> ''
AND id_user=?
ORDER BY p.id_network, p.post_date DESC
LIMIT ?, ?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getPostsByUserId',
			'params' => array('id_user', 'lbound', 'ubound'),
			'param_types' => 'iii',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Post',
			'returning_cols' => array('id', 'id_user', 'id_network', 
				'post_date', 'post_text', 'post_class', 
				'post_original', 'email', 'username', 
				'first_name', 'last_name', 'img_link', 
				'network_class', 'city_cur', 'region_cur', 'country_cur',
				'city_origin', 'region_origin', 'country_origin', 'language_origin',
			)
		));
		$m->setConnection($con);
		return $m;
	};

	$obj->insertPost = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
INSERT INTO posts
(id_user, id_network, post_date, post_text, post_class) 
VALUES (?, ?, NOW(), ?, ?)
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getPostsByUserId',
			'params' => array('id_user', 'id_network', 'post_text',
				'post_class'),
			'param_types' => 'nnsss',
			'nullable' => array(),
			'returning' => false,
			'returning_list' => false,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => null,
			'returning_cols' => null 
		));
		$m->setConnection($con);
		return $m;
	};

	$obj->deletePost = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
DELETE FROM posts
WHERE id=?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'deletePost',
			'params' => array('id'),
			'param_types' => 'n',
			'nullable' => array(),
			'returning' => false,
			'returning_list' => false,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => null,
			'returning_cols' => null 
		));
		$m->setConnection($con);
		return $m;
	};

	$obj->createReply = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
INSERT INTO post_replies
(id_parent, id_user, id_network, reply_text) 
VALUES (?, ?, ?, ?)
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'createReply',
			'params' => array('id_parent', 'id_user', 'id_network', 'reply_text'),
			'param_types' => 'nnns',
			'nullable' => array(),
			'returning' => false,
			'returning_list' => false,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => null,
			'returning_cols' => null 
		));
		$m->setConnection($con);
		return $m;
	};

	$obj->deleteReply = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
DELETE FROM post_replies
WHERE id=?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'deleteReply',
			'params' => array('id'),
			'param_types' => 'n',
			'nullable' => array(),
			'returning' => false,
			'returning_list' => false,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => null,
			'returning_cols' => null 
		));
		$m->setConnection($con);
		return $m;
	};
}
