<?php
	include("../environment.php");
	$cm = new Environment();

	// Basic test for mobile device
	$mobile_detect = new \misc\MobileDetect();

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('devlab' . $cm->ds . 'templates' . $cm->ds .'mobiletest.html', array(
		'vars' => $cm->getVars(),
		'mobile' => $mobile_detect->isMobile()
	));
?>
