<?php
	include("../environment.php");
	$cm = new Environment();

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('devlab' . $cm->ds . 'templates' . $cm->ds .'searchbar.html', array(
		'vars' => $cm->getVars(),
	));
?>
