<?php 

	ini_set('memory_limit', '512M');
	include '../environment.php';
	$cm = new \Environment();
	//$cm->displayErrors();

	$cm->enableDatabase($dal, $do2db);

	/*
	 * Okay, this is a nice, compact way to fill the key array
	 */
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

	//$languages = $do2db->execute($dal, NULL, 'getAllLanguages');
	//$cities = $do2db->execute($dal, NULL, 'getAllCities');
	$regions = $do2db->execute($dal, NULL, 'getAllRegions');
	//$countries = $do2db->execute($dal, NULL, 'getAllCountries');

	$cm->closeConnection();

	// Format language data
	//
	$key_data = array();

	foreach ($languages as $l) {

		$keys = \misc\Util::DoubleMetaphone($l->name);

		array_push($key_data, fillKeyArray($l, $keys['primary'], 'language'));

		if ($keys['secondary'] !== NULL) {
			array_push($key_data, fillKeyArray($l, $keys['secondary'], 'language'));
		}
	}

	/*
	foreach($cities as $ci) {

		$keys = \misc\Util::DoubleMetaphone($ci->name);

		array_push($key_data, fillKeyArray($ci, $keys['primary'], 'city'));

		if ($keys['secondary'] !== NULL) {
			array_push($key_data, fillKeyArray($ci, $keys['secondary'], 'city'));
		}
	}
	 */

	foreach($regions as $ri) {

		$keys = \misc\Util::DoubleMetaphone($ri->name);

		array_push($key_data, fillKeyArray($ri, $keys['primary'], 'region'));

		if ($keys['secondary'] !== NULL) {
			array_push($key_data, fillKeyArray($ri, $keys['secondary'], 'region'));
		}
	}

	/*
	foreach($countries as $ci) {

		$keys = \misc\Util::DoubleMetaphone($ci->name);

		array_push($key_data, fillKeyArray($ci, $keys['primary'], 'country'));

		if ($keys['secondary'] !== NULL) {
			array_push($key_data, fillKeyArray($ci, $keys['secondary'], 'country'));
		}
	}
	 */

	$batch_insert = new dal\SqlBatchInsert('search_keys', array('key', 'city_id', 'city_name', 'region_id', 'region_name', 'country_id', 'country_name', 'language_id', 'language_name', 'class_searchable'));

	$output = $batch_insert->getQuery($key_data);

	//Generate text file on the fly
	// Set headers
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=savethis.sql");
	header("Content-type: text/sql");

	// for now
	print $output;
	exit();
?>
