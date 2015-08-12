<?php
	include 'environment.php';
	$cm = new Environment();

	$client_vars = array(
		'title' => 'ADMIN PANEL (locations)',
		'home_path' => HOME_PATH,
		'l_brace' => '{{',
		'r_brace' => '}}'
	);

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
	$template = file_get_contents('templates/apply.html');

	// render
	echo $m->render($template, array(
					'vars' => $cm->getVars(),
					'client_vars' => $client_vars
						));
?>
