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

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getLocationsByName = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM cities
WHERE name LIKE ?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getLocationsByName',
			'params' => array('name'),
			'param_types' => 's',
			'returning' => true,
			'returning_list' => True,
			'returning_class' => 'dobj\City',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Passed in to get Complete User with Id
	 * Uses :> getting logged in user, getting profile user
	 */
	$obj->getLanguagesByName = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM languages
WHERE name LIKE ?
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getLanguagesByName',
			'params' => array('name'),
			'param_types' => 's',
			'returning' => True,
			'returning_list' => True,
			'returning_class' => 'dobj\Language',
			'returning_cols' => array()
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

	/*
	 * Must have download all cities
	 */
	$obj->getAllCities = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM cities
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getAllCities',
			'params' => array(),
			'param_types' => '',
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

	/*
	 * Must have download all regions 
	 */
	$obj->getAllRegions = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM regions 
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getAllRegions',
			'params' => array(),
			'param_types' => '',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\Region',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Must have download all countries
	 */
	$obj->getAllCountries = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM countries 
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getAllCountries',
			'params' => array(),
			'param_types' => '',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\Country',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Must have download all languages
	 */
	$obj->getAllLanguages = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
SELECT *
FROM languages
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'getAllLanguages',
			'params' => array(),
			'param_types' => '',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\Language',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Location Search gets Info Stuff
	 */
	$obj->LocationInfoSearchById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
CALL location_info_search_by_id(?)
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'LocationInfoSearchById',
			'params' => array('id_string'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\LocationResult',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Language Search gets Info Stuff
	 */
	$obj->LanguageInfoSearchById = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
CALL language_info_search_by_id(?)
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'LanguageInfoSearchById',
			'params' => array('id_string'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\Language',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	/*
	 * Key searches
	 *
	 * 1) Location, single key
	 * 2) Location, double key
	 * 3) Language, single key
	 * 4) Language, double key
	 */
	$obj->LocationSingleKeySearch = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
CALL location_single_key_search(?)
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'LocationInfoSearchById',
			'params' => array('key'),
			'param_types' => 's',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\LocationResult',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->LocationDoubleKeySearch = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
CALL location_double_key_search(?, ?)
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'LocationDoubleKeySearch',
			'params' => array('key'),
			'param_types' => 'ss',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\LocationResult',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->LanguageSingleKeySearch = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
CALL language_single_key_search(?)
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'LanguageSingleKeySearch',
			'params' => array('key'),
			'param_types' => 'ss',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\Language',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};

	$obj->LanguageDoubleKeySearch = function($con=NULL) {

		$m = new dal\DBQuery();

		$m->setValues(array(
			'query' => <<<SQL
CALL language_double_key_search(?, ?)
SQL
		/////////////////////////////////
		,	'test_query' => <<<SQL
				test
SQL
		/////////////////////////////////
		,	'name' => 'LanguageDoubleKeySearch',
			'params' => array('key'),
			'param_types' => 'ss',
			'nullable' => array(),
			'returning' => true,
			'returning_value' => False,
			'returning_assoc' => False,
			'returning_list' => True,
			'returning_class' => 'dobj\Language',
			'returning_cols' => array()
		));

		$m->setConnection($con);

		return $m;
	};
}
?>
