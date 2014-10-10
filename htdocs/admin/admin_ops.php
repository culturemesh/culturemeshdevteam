<?php

/*
 * @file - admin_ops.php 
 * @error:
 * 	- 0, no error
 * 	- >0 , include error message
 */

//namespace Respect\Validation\Validator;

require '../vendor/autoload.php';
include_once '../zz341/fxn.php';

include_once '../error_pool.php';

// data files
include_once '../data/dal_query_handler.php';
include_once '../data/dal_meta.php';
include_once '../data/dal_text_data.php';
include_once '../data/dal_location.php';
include_once '../data/dal_language.php';

include_once '../data/loc_item.php';

ini_set('display_errors', true);
error_reporting(E_ALL ^ E_NOTICE);

$json_post = json_decode($HTTP_RAW_POST_DATA, true);

$location_tables = array('cities', 'regions', 'countries');

$network_suffixes = array('_cur', '_origin');

$searchable_singular = array(
	'cities' => 'city',
	'regions' => 'region',
	'countries' => 'country',
	'languages' => 'language'
);

$sqlTypeDict = array (
	'name' => 'string',
	'id' => 'int',
	'region_id' => 'int',
	'region_name' => 'string',
	'country_id' => 'int',
	'country_name' => 'string',
	'latitude' => 'float',
	'longitude' => 'float',
	'population' => 'int',
	'num_speakers' => 'int',
	'feature_code' => 'string',
	'featureCode' => 'string',
	'added' => 'int',
	'iso_a2' => 'string'
);

/*
function addSqlQuotes($data) {

	foreach( $data as $key => $value ) {
		
		if ($sqlTypeDict[$key] == 'string')
			$value = "'".$value."'";
	}

	return $data;
}
 */

if ($json_post['op'] == 'login') {
	
	$response = array(
		'error' => NULL
	);

	if ($json_post['password'] == DB_PASS) {
		$response['error'] = 0;
	}
	else {
		$response['error'] = 1;
		$response['error_msg'] = 'Sorry, that password is incorrect.';
	}

	echo json_encode($response);
	exit();
}
if ($_POST['op'] == 'getTableStructure') {
	
	// get the necessary functions
	$con = QueryHandler::getDBConnection();

	$response = array(
		'error' => NULL,
		'description' => NULL,
		'keys' => array('COLUMN_NAME', 'DATA_TYPE', 'IS_NULLABLE', 'COLUMN_DEFAULT', 'CHARACTER_MAXIMUM_LENGTH', 
			'NUMERIC_PRECISION', 'NUMERIC_SCALE', 'FK')
	);

	// get table structure
	$response['description'] = Meta::describeTable($_POST['table'], $con);

	// handle the error things
	if ($response['description'] == false) {
		$response['error'] = 1;
		$response['error_msg'] = 'Could not get description of table';
	}
	else
		$response['error'] = 0;

	echo json_encode($response);
}

if($json_post['op'] == 'create' && $json_post['singobatch'] == 'single') {

	// create mustache template
	$m = new Mustache_Engine;

	$template = file_get_contents('templates/sql-insert.sql');

	// execute query

	// retrive new city

	// create new neighbors for location (OPTIONAL)
//	Location::getNearbyStuff($data);

	// rewrite files
	//TextData::rewrite($json_post['table_name']);
	//
}

else if ($json_post['op'] == 'update' && $json_post['singobatch'] == 'single') {

	// execute query
	$m = new Mustache_Engine;

	$template = file_get_contents('templates/sql-update.sql');

	$query = $m->render($template, array(
		'table_name' => $json_post['table'],
		'nv' => array(),
		'terminal' => NULL,
		'params' => array()
	));

	// add things
	$con = QueryHandler::getDBConnection();
	$result = QueryHandler::executeQuery($query);

	// update networks (OPTIONAL -- name mod)

	// rewrite files (OPTIONAL -- name mod)
	
	// create new neighbors for city (OPTIONAL -- lat,long mod)

}

else if ($json_post['op'] == 'searchSearchables') {

	// response
	$response = array(
		'error' => NULL,
		'description' => NULL,
		'keys' => array('COLUMN_NAME', 'DATA_TYPE', 'IS_NULLABLE', 'COLUMN_DEFAULT', 'CHARACTER_MAXIMUM_LENGTH', 
			'NUMERIC_PRECISION', 'NUMERIC_SCALE', 'FK')
	);

	// get location shiz
	$values = explode(', ', $json_post['query']);

	switch ($json_post['table'])
	{
	case 'languages':
		$srbl = Language::getLanguageByNameF($values[0]);
		break;
	case 'cities':
		$srbl = Location::getCCByNameF($values[0], $values[1], $values[2]);
		break;
	case 'regions':
		$srbl = Location::getRCByNameF($values[0], $values[1]);
		break;
	case 'countries':
		$srbl = Location::getCOByNameF($values[0], $values[1]);
		break;
	}

	// initialize array
	$cols = array();

	// make right for jscript down below
	foreach ($srbl as $key => $value) {
		$col = array(
			'COLUMN_NAME' => $key,
			'value' => $value,
			'class' => NULL
		);

		// if the string is an id, it's untouchable
		if (strpos($key, 'id') > -1)
			$col['class'] = 'imp';
		else if (strpos($key, '_name') > -1)
			$col['class'] = 'imp_fk';
		else
			$col['class'] = 'bleh';

		// add to cols
		array_push($cols, $col);
	}

	$response['error'] = 0;
	$response['description'] = $cols;

	// return
	echo json_encode($response);
}

else if ($json_post['op'] == 'rewriteTxt') {

	// create response
	$response = array(
		'error' => NULL
	);

	// rewrite everything
	TextData::rewrite();

	// update response
	$response['error'] = 0;

	// send response
	echo json_encode($response);
	exit();
}

else if ($json_post['op'] == 'MP' && $json_post['operation'] == 'test') {

	// create response
	$response = array(
		'error' => NULL,
		'error_msg' => NULL
	);

	// ERROR POOL TEST
	$ep = new ErrorPool();
	$ep->addError( new CMError(
		true,
		'First error',
		'First error')
	);

	$ep->checkStop($response);

	$ep->addError( new CMError(
		false,
		'2nd error',
		'2nd error')
	);

	$ep->checkError($response);

//	exit();
}

else if ($json_post['op'] == 'MP' && $json_post['singobatch'] == 'single') 
{
	// set up response object
	$response = array(
		'error' => NULL,
		'query' => NULL,
		'data' => NULL
	);

	// make a data object for use in queries to come
	$data = $json_post['data'];

	$mod_cols = array();
		
//	$sql_data = addSqlQuotes($data);

	switch ($json_post['operation']){
	case 'create':
		$template = file_get_contents('../templates/sql-insert.sql');

		// init array
		$vals = array();
		$cols = array();

		// put keys and values in cols and vals arrays respectively
		foreach( $data as $key => $value) {
			if ($key == 'id')
				continue;

			if ($sqlTypeDict[$key] == 'string')
				$value = "'".$value."'";

			array_push($cols, $key);
			array_push($vals, $value);
		}

		$tcol = array_pop($cols);
		$tval = array_pop($vals);

		$template_data = array(
			'table_name' => $json_post['table'],
			'col_names' => array(
				'cols' => $cols,
				'tcol' => $tcol
			),
			'row_items' => array(
				'trow' => array(
					'values' => array(
						'vals' => $vals,
						'tval' => $tval 
						)
					)
				)
			);
		break;

	case 'update':

		// get update template
		$template = file_get_contents('../templates/sql-update.sql');

		// get modcols
		$mod_cols = $json_post['modCols'];

		// exit without mods
		if (count($mod_cols) == 0) {
			$json_response['error'] = 'No columns were modified';
			echo json_encode($json_response);
			exit();
		}

		// arrange only modified columns
		// into fun array type deal
		$cols = array();

		for ($i = 0; $i < count($mod_cols); $i++) {


			$value = $data[$mod_cols[$i]];

			if ($sqlTypeDict[$mod_cols[$i]] == 'string')
				$value = "'".$value."'";

			//
			$new_value = array(
				'col' => $mod_cols[$i],
				'value' => $value
			);

			array_push($cols, $new_value);
		}

		// pop last pair off of columns
		$tcol = array_pop($cols);

		// data for the template
		$template_data = array(
			'table_name' => $json_post['table'],
			'nv' => $cols,
			'terminal' => $tcol,
			'param' => array(
				'fp' => 'id',
				'fp_val' => $data['id']
			),
			'more' => array()
		);

		break;
	}



	$m = new Mustache_Engine;

	$stmt = $m->render($template, $template_data);
	$response['data'] = $template_data;
	$response['query'] = $stmt;

	$jtable = NULL;

	/// CREATE A FAKE TABLE FOR LATER
	if (in_array('latitude', $mod_cols)
		|| in_array('longitude', $mod_cols)
		|| $json_post['operation'] == 'create') {

		// create fake table to make stuff
		$jtable = Meta::createJunkCopy($json_post['table']);
	}

	$con = QueryHandler::getDBConnection();

	// EXECUTE QUERY
	$response['error'] = QueryHandler::executeQuery($stmt, $con);


	/* may need to
	 * a) Update network names
	 * b) Update nearby locations
	 * c) update text files
	 */

	// UPDATE NEARBY LOCATIONS
	if (in_array('latitude', $mod_cols)
		|| in_array('longitude', $mod_cols)
		|| ($json_post['operation'] == 'create' 
		&& in_array($json_post['table'], $location_tables))) {
		
		// make a loc item out of the thing
		$loc = new LocItem($searchable_singular[$json_post['table']],
				$json_post['table']);

		// insert into junk table
		$template_data['table_name'] = $jtable;
		$stmt = $m->render($template, $template_data);

	 	QueryHandler::executeQuery($stmt, $con);

		// get back from junk table and parse
		$result = Meta::getJunkDuplicate($jtable, $con);

		while($row = mysqli_fetch_array($result))
			$loc->fillFromRow($row);

		// delete the now useless junk table
		Meta::deleteJunkCopy($jtable, $con);

		// delete former nearby stuff
		Location::deleteNearbyStuff($loc->getId(), $json_post['table'],
			$searchable_singular[$json_post['table']]);

		// create NEW nearby stuff
		Location::getNearbyStuff($loc, $con);
	}

	$id = $json_post['data']['id'];
	$name = $json_post['data']['name'];
	$class = $searchable_singular[$json_post['table']];

	// UPDATE NETWORK NAMES
	if (in_array('name', $mod_cols)) {
		
		// modify networks
		if ( $json_post['table'] == 'languages') {

			echo Language::updateNetworkNames($id, $name, $con);
		}
		else if ( in_array($json_post['table'], $location_tables)) {

			echo Location::updateNetworkNames($id, $name, $class, $con);

			// update children, regions and cites if country
			if ($json_post['table'] == 'countries')
			{
				echo Location::updateChildrenNames($id, $name, 'regions', 'country', $con);
				echo Location::updateChildrenNames($id, $name, 'cities', 'country', $con);
			}

			// just cities if region
			if ($json_post['table'] == 'regions') {
				echo Location::updateChildrenNames($id, $name, 'cities', 'region', $con);
			}


		}
	}


	// UPDATE NETWORK PARENTS
	//  	(1) region
	if (in_array('region_id', $mod_cols)) {

		$pid = $json_post['data']['region_id'];
		$pname = $json_post['data']['region_name'];
		$pclass = 'region';

		Location::updateNetworkParent($id, $class, $pid, $pname, $pclass, $con);
	}

	// 	(2) country
	if (in_array('country_id', $mod_cols)) {

		$pid = $json_post['data']['country_id'];
		$pname = $json_post['data']['country_name'];
		$pclass = 'country';

		echo Location::updateNetworkParent($id, $class, $pid, $pname, $pclass, $con);
	}

	// close dbj connection
	mysqli_close($con);

	// (3)update text data
	if (in_array(array('name', 'region_id', 'country_id'),
		$mod_cols) 
		|| $json_post['operation'] == 'create') 
	{
		TextData::rewrite($json_post['table']);
	}

	// return
	echo json_encode($response);
}

//else if ($json_post['op'] == 'MP' && $json_post['type'] == 'single')
/*
if ($_POST['op'] == 'ts-select') {

}
else if ($_POST['op'] == 'op-select') {

}
 */
else if ($_POST['op'] == 'ins-parse') {

	// declare validation

	$response = array(
		'error' => NULL,
		'table_cols' => NULL,
		'file_cols' => NULL,
		'keys' => array('COLUMN_NAME', 'DATA_TYPE', 'IS_NULLABLE', 'COLUMN_DEFAULT', 'CHARACTER_MAXIMUM_LENGTH', 
			'NUMERIC_PRECISION', 'NUMERIC_SCALE', 'FK')
	);

	// get table structure
	$response['table_cols'] = Meta::describeTable($_POST['table'], $con);

	// load file into temp object
	//  - to validate for later
	$tmp = $_FILES['object'];

	// parse file as string
	$file =  file_get_contents($tmp['tmp_name']);

	// test to see if it's valid json
	/*
	if (!Respect\Validation\Validator::json()->validate($file)) {
		$json_response['error'] = 'Not a json file.';

		echo json_encode($json_response);
	//	exit();	
	}
	 */

	// turn into php object
	$json_file = json_decode($file, true);

	// get keys from first elem of json file
	$keys = array_keys($json_file[0]);

	// load json of db stuff
	$file = file_get_contents('reqd-fields.json');
	$table_fields = json_decode($file, true);

	$response['error'] = 0;
	$response['cols'] = $keys;

	echo json_encode($response);
	exit();
}
else if ($_POST['op'] == 'update' && $_POST['singobatch'] == 'batch') {

	$response = array(
		'error' => NULL,
		'data' => NULL
	);

	// grab post data
	$col_data = $_POST['columnData'];
	$col_data = json_decode($col_data, true);
	$col_map = json_decode($_POST['colmap'], true);

	// GET DATA
	// load file
	$tmp = $_FILES['object'];
	
	// parse file as string
	$file =  file_get_contents($tmp['tmp_name']);

	// turn into php object
	$items = json_decode($file, true);

	$mod_cols = explode(',', $_POST['mod_cols']);

	// build update statements
	$update_batch = array(
		'table_name' => $_POST['table'],
		'change_col' => NULL,
		'batch_vals' => array(),
		'mcols' => array()
	);

	// create the first section
	$mc_1 = array_shift($mod_cols);
	$id_col = $col_map['id'];

	$count = 0;

	foreach ($items as $item) {

		if ($count >= 5)
			break;
		
		// get change_col
		$update_batch['change_col'] = $mc_1;

		// get corresponding column name for file
		$tran_col = $col_map[$mc_1];

		$change_val = $item[$tran_col];

		if ($sqlTypeDict[$mc_1] == 'string')
			$change_val = "'".$change_val."'";

		$batch_val = array(
			'id_col' => 'id = '.$item[$id_col],
			'change_val' => $change_val
		);

		array_push($update_batch['batch_vals'], $batch_val);

		$count++;
	}

	// create a new section of the query
	// for each consecutive modified column
	foreach ($mod_cols as $mc) {
		
		// an mcol is another column that is being
		// modified in the batch
		//
		// needs input from each item
		$mcol = array(
			'mchange_col' => $mc, // db column name
			'mbatch_vals' => array()
		);

		// get corresponding column name for file
		$tran_col = $col_map[$mc];

		$count = 0;

		foreach ($items as $item) {

			if ($count >= 5)
				break;

			$mchange_val = $item[$tran_col];

			if ($sqlTypeDict[$mc] == 'string')
				$mchange_val = "'".$mchange_val."'";

			$batch_val = array(
				'id_col' => 'id = '.$item[$id_col],
				'mchange_val' => $mchange_val
			);

			// push into mcol
			array_push($mcol['mbatch_vals'], $batch_val);

			$count++;
		}

		array_push($update_batch['mcols'], $mcol);
	}

	/*
	$row_values = array(
		'values' => array(
			'vals' => array(),
			'tval' => NULL
		)
	);
	 */


	// start mustache
	$m = new Mustache_Engine;

	// get sql template
	$template = file_get_contents('../templates/sql-update-batch.sql');

	// generate statement
	$stmt = $m->render($template, $update_batch);

	$response['data'] = $stmt;

	// (1)run query
	$con = QueryHandler::getDBConnection();
	echo $stmt;
	$response['error'] = QueryHandler::executeQuery($stmt, $con);
	
	// (2) if location, update location data to add nearby locations
	if (in_array(array('latitude', 'longitude'), $mod_cols)) {
		// create junk table
		$jtable = Meta::createJunkCopy($insert_batch['table_name'], $con);
	
		$insert_batch['table_name'] = $jtable;
		$stmt = $m->render($template, $insert_batch);
	 	QueryHandler::executeQuery($stmt, $con);

		// get auto increment
		Location::getNearbyStuff($loc_items, $con);

		// delete the now useless junk table
		Meta::deleteJunkCopy($jtable, $con);
	}

	// (3) update network names
	/*
	if (in_array('name', $mod_cols)) {

		// get singular form
		$class = $searchable_singular[$_POST['table']];

		// get name and id translation
		$name_tran = $col_map['name'];
		$id_tran = $col_map['id'];

		// build update statements
		$update_batch = array(
			'table_name' => 'networks',
			'change_col' => $class.'_origin',
			'batch_vals' => array(),
			'mcols' => array(
				'mchange_col' => $class,
				'mbatch_vals' => array())
		);



		// 
		for ($i = 0; $i < count($network_suffixes); $i++) {

			// skip cur if you're a language
			// -- language got no cur
			if ($_POST['table'] == 'languages' 
			&& $i == 0) 
				continue;

			foreach ($items as $item) {
				
				// get suffix
				$ns = $network_suffixes[$i];

				$id_col = 'id_'.$class.$ns.' = '. $item[$id_tran];
				$batch_val = array(
					'id_col' => $id_col,
					'change_val' => "'".$item[$name_tran]."'"
				);

			} // end foreach

		} //endfor

	} //endif
	 */
	mysqli_close($con);

	// (4)update text data
	TextData::rewrite($insert_batch['table_name']);

	echo json_encode($response);
}
else if ($_POST['op'] == 'create' && $_POST['singobatch'] == 'batch') {

	$response = array(
		'error' => NULL,
		'data' => NULL
	);

	// grab post data
	$col_data = $_POST['columnData'];

	$col_data = json_decode($col_data, true);

	// build insert statements

	$insert_batch = array(
		'table_name' => NULL,
		// before VALUES
		'col_names' => array(
			'cols' => array(),
			'tcol' => NULL
		),
		// after VALUES
		'row_items' => array(
			'rows' => array(), // array of row values
			'trow' => NULL // single row value
		)
	);

	/*
	$row_values = array(
		'values' => array(
			'vals' => array(),
			'tval' => NULL
		)
	);
	 */

	// get table name
	$insert_batch['table_name'] = $_POST['table'];

	// get column names
	for ($i = 0; $i < count($col_data); $i++) {

		// assign terminal column
		if (count($col_data) - $i == 1)
			$insert_batch['col_names']['tcol'] = $col_data[$i]['dbcol'];

		else {
			array_push($insert_batch['col_names']['cols'], $col_data[$i]['dbcol']);
		}
	}

	// GET DATA
	// load file
	$tmp = $_FILES['object'];
	
	// parse file as string
	$file =  file_get_contents($tmp['tmp_name']);

	// turn into php object
	$items = json_decode($file, true);

	// get keys
	$keys = array_keys($items[0]);

	$loc_items = array();

	$test_count = 0;
	foreach ($items as $item) {

		// transform into locitem for later use
		{
			// create stuff
			$table = $_POST['table'];
			$lc = new LocItem($searchable_singular[$table],
				$table);

			// fill
			$lc->fillFromRow($item);

			// push into array
			array_push($loc_items, $lc);
		}


		// for test purposes, god save the short stuff
		if ($test_count >= 5)
			break;

		$row_values = array(
			'values' => array(
				'vals' => array(),
				'tval' => NULL
			)
		);

		// get column names
		// should match columns in insert stmt
		for ($i = 0; $i < count($col_data); $i++) {

			$val = $item[$col_data[$i]['inscol']];
			$col = $col_data[$i]['dbcol'];

			// add string if appropriate
			if($sqlTypeDict[$col] == 'string') {
				$val = "'".$val."'";
			}

			// if end, assign terminal value
			if (count($col_data) - $i == 1)
				$row_values['values']['tval']  
				= $val;
			else {
				array_push($row_values['values']['vals'], $val);
			}
		}

		// post loop push
		if (5 - $test_count > 1) 
			array_push($insert_batch['row_items']['rows'], $row_values);
		else
			$insert_batch['row_items']['trow'];

		$test_count++;
	}

	// start mustache
	$m = new Mustache_Engine;

	// get sql template
	$template = file_get_contents('../templates/sql-insert.sql');

	// generate statement
	$stmt = $m->render($template, $insert_batch);

	$response['data'] = $stmt;

	$con = QueryHandler::getDBConnection();

	// PREPARE JUNK TABLE FOR LATER
	$jtable = NULL;
	if (in_array($table, $location_tables)) {
		// create junk table
		$jtable = Meta::createJunkCopy($insert_batch['table_name']);
	}


	// (1) RUN MAIN QUERY
	//
	echo $stmt;
	$response['error'] = QueryHandler::executeQuery($stmt, $con);

	// possibly maybe update nearby stuff
	// NOT RECOMMENDED
	if ( $in_array($table, $location_tables)) {

		// insert into junk table
		$insert_batch['table_name'] = $jtable;
		$stmt = $m->render($template, $insert_batch);
		QueryHandler::executeQuery($stmt);
	
		// get auto increment
		Location::getNearbyStuff($loc_items);

		Meta::deleteJunkCopy($jtable);
	}


	// (3)update text data
	TextData::rewrite($table);
	
	echo json_encode($response);
}

?>
