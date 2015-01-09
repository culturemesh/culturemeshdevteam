<?php

function registerImage($obj) {

	$obj->insertImage = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL

			INSERT INTO images (hash, post, event, profile)
			VALUES (?, ?, ?, ?)
SQL

		////////////////////////////////////////
		,	'test_query' => null
		,	'name' => 'insertImage',
			'params' => array('hash', 'post', 'event', 'profile'),
			'param_types' => 'snnn',
			'nullable' => array('post', 'event', 'profile'),
			'returning' => false,
			'returning_list' => False,
			'returning_class' => null, 
			'returning_cols' => null
		));

		$m->setConnection($con);
		return $m;
	};

	/*
	$obj->insertImage = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL

			INSERT INTO user_images (hash, post, event, profile)
			VALUES (?, ?, ?, ?)
SQL

		////////////////////////////////////////
		,	'test_query' => null
		,	'name' => 'insertImage',
			'params' => array('hash', 'post', 'event', 'profile'),
			'param_types' => 'snnn',
			'nullable' => array('post', 'event', 'profile'),
			'returning' => false,
			'returning_list' => False,
			'returning_class' => null, 
			'returning_cols' => null
		));
		$m->setConnection($con);
		return $m;
	}
	 */
}

?>
