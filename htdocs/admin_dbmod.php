<?php

	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);

	include 'environment.php';
	$cm = new Environment();

	include 'debug.php';

	// include db stuff
	include_once 'zz341/fxn.php';
	include_once 'data/dal_meta.php';

	// include templates
//	include 'twig.php';
	require 'vendor/autoload.php';

	/* Initially!!!
	 *  MUST
	 *  1) Get db data from database,
	 *  	that is tables in the ktc db
	 *  2) Send to Template for fillin up
	 */

//	$tables = Meta::getTables();
	$tables = array('languages',
		'cities',
		'regions',
		'countries'
	);

	/*
	$variable = array(
		'title' => 'ADMIN PANEL (locations)',
		'home_path' => HOME_PATH,
		'l_brace' => '{{',
		'r_brace' => '}}'
	);
	 */
	
	/*
	 * PLAY EXAMPLE
	 *
	$m = new Mustache_Engine(array(
	  'pragmas' => array(Mustache_Engine::PRAGMA_BLOCKS),
	  'partials' => array(
	    'jim' => 'Hello {{$ planet }}planet{{/ planet }}'
	  ),
	));


	echo $m->render('{{< jim }}{{$ planet }}World!{{/ planet }}{{/ jim }}', array());

	 */
	/*

	$m = new Mustache_Engine(array(
		'partials' => array(
			'parent' => $parent 
		)
	));

	$template = file_get_contents('templates/admin_dbmod.html');
	echo $m->render($template, array(
					'vars' => $variable,
					'tables' => $tables));

	 */

	// get base layout
	$base = file_get_contents('templates/base.html');

	// get engine
	$m = new Mustache_Engine(array(
	  'pragmas' => array(Mustache_Engine::PRAGMA_BLOCKS),
	  'partials' => array(
	    'layout' => $base
	  ),
	));

	// get actual site
	$template = file_get_contents('templates/admin_searchmod.html');

	// render
	echo $m->render($template, array(
		 			'vars' => $cm->getVars(),
					'tables' => $tables,
					'booger' => array(
						'snatch' => true,
						'grab' => false)));
?>
