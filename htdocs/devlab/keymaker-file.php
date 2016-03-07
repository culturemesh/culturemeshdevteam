<?php 
	ini_set('memory_limit', '512M');
	include '../environment.php';
	$cm = new \Environment();
	$keymaker = new \misc\Keymaker($cm);
	//$cm->displayErrors();

	$cm->enableDatabase($dal, $do2db);

	$table = $_GET['generate'];

	//
	// Okay, this is a nice, compact way to fill the key array
	// 
	function fillKeyArray($item, $key, $type) {

		$id_suffix = '_id';
		$name_suffix = '_name';

		$id_key = $type . $id_suffix;
		$name_key = $type . $name_suffix;

		$obj_array = array();
		$obj_array[$id_key] = $item->id;
		$obj_array[$name_key] = \misc\Util::DoubleQuote( $item->name );
		$obj_array['class_searchable'] = \misc\Util::Quote( $type );
		$obj_array['key'] = \misc\Util::Quote( $key );


		if ($type == 'city' || $type == 'region') {

			$obj_array['country_id'] = $item->country_id;
			$obj_array['country_name'] = \misc\Util::DoubleQuote( $item->country_name );
		}


		if ($type == 'city') {
			$obj_array['region_id'] = $item->region_id;
			$obj_array['region_name'] = \misc\Util::DoubleQuote($item->region_name);
		}

		return $obj_array;
	}

	// Guide for
	//  DB call
	$full_guide = array(
		'cities' => array(
			'query_name' => 'getAllCities',
			'type' => 'city',
			'filename' => 'city-keys.sql'
		),
		'regions' => array(
			'query_name' => 'getAllRegions',
			'type' => 'region',
			'filename' => 'region-keys.sql'
		),
		'countries' => array(
			'query_name' => 'getAllCountries',
			'type' => 'country',
			'filename' => 'country-keys.sql'
		),
		'languages' => array(
			'query_name' => 'getAllLanguages',
			'type' => 'language',
			'filename' => 'language-keys.sql'
		)
	);

	$guide = $full_guide[$table];

	$key_data = array();

	$objects = $do2db->execute($dal, NULL, $guide['query_name']);
	$cm->closeConnection();

	foreach($objects as $object) {

		$keys = $keymaker->generateKeys( $object->name );

		foreach ($keys as $key) {
		  array_push($key_data, fillKeyArray($object, $key, $guide['type']));
		}
	}

	$batch_insert = new dal\SqlBatchInsert('search_keys', array('key', 'city_id', 'city_name', 'region_id', 'region_name', 'country_id', 'country_name', 'language_id', 'language_name', 'class_searchable'));

	$output = $batch_insert->getQuery($key_data);

	//Generate text file on the fly
	// Set headers
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=" . $guide['filename']);
	header("Content-type: text/sql");

	// for now
	print $output;
	exit();
?>
