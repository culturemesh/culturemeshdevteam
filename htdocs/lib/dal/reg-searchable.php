<?php

function registerSearchable($obj) {

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getCityById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM cities
WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getCityById',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => 'dobj\City',
			'returning_cols' => array('id', 'name', 'latitude', 'longitude',
					'region_id', 'region_name', 'country_id', 'country_name',
					'population', 'feature_code', 'tweet_terms', 'region_tweet_terms',
					'country_tweet_terms')
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getRegionById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM regions
WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getRegionById',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => 'dobj\Region',
			'returning_cols' => array('id', 'name', 'latitude', 'longitude',
					'region_id', 'region_name', 'population', 'feature_code', 
					'tweet_terms', 'country_tweet_terms')
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getCountryById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM countries
WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getCountryById',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => 'dobj\Country',
			'returning_cols' => array('id', 'name', 'latitude', 'longitude',
					'population', 'feature_code', 'tweet_terms')
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getLanguageById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM languages
WHERE id=?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getLanguageById',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => False,
			'returning_class' => 'dobj\Language',
			'returning_cols' => array('id', 'name', 'num_speakers', 'added', 'tweet_terms')
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->getPopulousCitiesByCountry = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM cities
WHERE country_id=? 
ORDER BY population DESC
LIMIT 0,5
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getPopulousCitiesByCountry',
			'params' => array('id'),
			'param_types' => 'i',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\City',
			'returning_cols' => array('id', 'name', 'latitude', 'longitude',
					'region_id', 'region_name', 'country_id', 'country_name',
					'population', 'feature_code', 'tweet_terms', 'region_tweet_terms',
					'country_tweet_terms')
		));

		$m->setConnection($con);

		return $m;
	};
}
?>
