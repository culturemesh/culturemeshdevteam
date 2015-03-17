<?php

function registerTweet($obj) {

	$obj->getTweetsByNetworkId = function($con=NULL) {

		$m = new dal\DBQuery();
		$m->setValues(array(
			'query' => <<<SQL
o
SELECT t.*, reply_count, hash
FROM tweet_posts t
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
ON t.id = hh.id_post
WHERE t.id_network=?
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
			'returning_class' => 'dobj\Tweet',
			'returning_cols' => array('id', 'id_user', 'id_network', 
				'post_date', 'post_text', 'post_class', 
				'post_original', 'email', 'username', 
				'first_name', 'last_name', 'img_link', 
				'reply_count', 'hash')

));
		$m->setConnection($con);
		return $m;
	};
}
