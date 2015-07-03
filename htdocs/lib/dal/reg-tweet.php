<?php

function registerTweet($obj) {

	/*
	 * Inserts a new post
	 *
	 */
	$obj->insertPostTweet = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
INSERT INTO post_tweets
(id_twitter, id_network, name, screen_name, text, profile_image_url, created_at) 
VALUES (?, ?, ?, ?, ?, ?, ?)
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'insertPostTweet',
			'params' => array('id_twitter', 'id_network', 'name', 'screen_name',
				'text', 'profile_image_url', 'created_at'),
			'param_types' => 'nnsssss',
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


	/*
	 * Inserts a reply to a tweet
	 *
	 */
	$obj->insertTweetReply = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
INSERT INTO post_tweet_replies
(id_parent, id_user, id_network, reply_date, reply_text) 
VALUES (?, ?, ?, NOW(), ?)
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'insertTweetReply',
			'params' => array('id_parent', 'id_user', 'id_network',
				'reply_text'),
			'param_types' => 'nnnss',
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

	/*
	 * Get tweet by id
	 *
	 */
	$obj->getTweetById = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT t.*
FROM post_tweets t
WHERE t.id=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getTweetById',
			'params' => array('id'),
			'param_types' => 'sii',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Tweet',
			'returning_cols' => array('id', 'id_network', 
				'name', 'screen_name', 'text', 'created_at'
			)

));
		$m->setConnection($con);
		return $m;
	};

	/*
	 * Get tweets by network id
	 *
	 */
	$obj->getTweetsByNetworkId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT t.*
FROM post_tweets t
WHERE t.id_network=?
ORDER BY created_at DESC
LIMIT ?, ?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getTweetsByNetworkId',
			'params' => array('id_network', 'lobound', 'upbound'),
			'param_types' => 'sii',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Tweet',
			'returning_cols' => array('id', 'id_network', 
				'name', 'screen_name', 'text', 'created_at'
			)

));
		$m->setConnection($con);
		return $m;
	};


	/*
	 * Get tweet replies by parent id
	 *
	 */
	$obj->getTweetRepliesByParentId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT p.*, u.email, u.username, u.first_name, u.last_name, u.img_link
FROM post_tweet_replies p, users u
WHERE p.id_user=u.id
AND p.id_parent=?
ORDER BY reply_date DESC
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getTweetRepliesByParentId',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\TweetReply',
			'returning_cols' => array('id', 'id_parent', 'id_user', 'id_network', 
				'reply_date', 'reply_text', 'email', 'username', 
				'first_name', 'last_name', 'img_link'
			)

		));

		$m->setConnection($con);
		return $m;
	};

	$obj->deleteTweet = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
DELETE FROM post_tweets
WHERE id=?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'deleteTweet',
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


	$obj->deleteTweetReply = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
DELETE FROM post_tweet_replies
WHERE id=?
SQL

		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'deleteTweetReply',
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


	/*
	 * Get tweets by network id
	 *
	 */
	$obj->getNetworkTweetQuery = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

SELECT *
FROM network_tweet_query_data
WHERE id_network=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'getNetworkTweetQuery',
			'params' => array('id_network'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_list' => true,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => 'dobj\Tweet',
			'returning_cols' => array('id_network', 'query_origin_scope', 'query_location_scope', 
				'query_level', 'query_auto_update', 'query_default',
				'query_since_date'
			)

));
		$m->setConnection($con);
		return $m;
	};

	/*
	 * Update tweet query
	 *
	 */
	$obj->updateNetworkTweetQuery = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

UPDATE network_tweet_query_data
SET 
query_origin_scope = ?,
query_location_scope = ?, 
query_level = ?, 
query_auto_update = ?,
query_default = ?, 
query_since_date = ?
WHERE id_network=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'updateNetworkTweetQuery',
			'params' => array('query_origin_scope', 'query_location_scope',
					'query_level', 'query_auto_update', 'query_default',
					'query_since_date', 'id'),
			'param_types' => 'iiiiisi',
			'nullable' => array(),
			'returning' => False,
			'returning_list' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => NULL,
			'returning_cols' => NULL

));
		$m->setConnection($con);
		return $m;
	};

	/*
	 * Update tweet query
	 *
	 */
	$obj->updateNetworkTweetCount = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

UPDATE network_tweet_query_data
SET tweet_count = ?
WHERE id_network=?
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'updateNetworkTweetCount',
			'params' => array('tweet_count', 'id'),
			'param_types' => 'ii',
			'nullable' => array(),
			'returning' => False,
			'returning_list' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => NULL,
			'returning_cols' => NULL

));
		$m->setConnection($con);
		return $m;
	};


	/*
	 * Inserts data for tweet adjustment
	 *
	 */
	$obj->insertTweetAdjustment = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL

INSERT INTO tweet_query_adjustments
(id_network, start_level, target_level, origin_scope_start,
 origin_scope_end, location_scope_start, location_scope_end,
 start_since_date, end_since_date, prev_query_relevance)
VALUES
(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
SQL
		/////////////////////////////
		, 	'test_query' => <<<SQL
SQL
		/////////////////////////////
		,	'name' => 'insertTweetAdjustment',
			'params' => array('id_network', 'start_level', 'target_level',
					'origin_scope_start', 'origin_scope_end',
					'location_scope_start', 'location_scope_end',
					'start_since_date', 'end_since_date',
					'prev_query_relevance'
					),
			'param_types' => 'iiiiiiisss',
			'nullable' => array('origin_scope_start', 'origin_scope_end',
				'location_scope_start', 'location_scope_end',
				'start_since_date', 'end_since_date',
				'prev_query_relevance'),
			'returning' => False,
			'returning_list' => False,
			'returning_value' => False,
			'returning_assoc' => false,
			'returning_class' => NULL,
			'returning_cols' => NULL

));
		$m->setConnection($con);
		return $m;
	};
}
