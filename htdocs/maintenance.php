<?php

	// set maintenance headers
	header('HTTP/1.1: 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 300');

	// load environment
	require('environment.php');
	$cm = new Environment();

	// get engine
	$m = new \Mustache_Engine(array(
		'pragmas' => array(\Mustache_Engine::PRAGMA_BLOCKS),
		'partials' => array(
			'layout' => $base
		),
	));

	// get actual site
	$template = file_get_contents(\Environment::$site_root . $cm->ds . 'templates' . $cm->ds . 'maintenance.html');
	$page_vars = array(
		'vars' => $cm->getVars()
	);

	// display the page proudly, chieftain
	echo $m->render($template, $page_vars);
?>
