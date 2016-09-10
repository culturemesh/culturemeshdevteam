<?php
	include("../environment.php");
	$cm = new Environment();
	
	// COMPONENTS
	$m_component = new \misc\MustacheComponent();

	$searchbar_template = file_get_contents('templates' . $cm->ds . 'searchbar.html');

	$sb_home = $m_component->render($searchbar_template, array('sb-home' => True));
	$sb_standard = $m_component->render($searchbar_template, array());
	$sb_alt_font = $m_component->render($searchbar_template, array('alt-font' => True));

	$page_loader = new \misc\PageLoader($cm, $mobile_detect);
	echo $page_loader->generate('devlab' . $cm->ds . 'templates' . $cm->ds .'searchbar.html', array(
		'vars' => $cm->getVars(),
		'searchbars' => array(
			'home' => $sb_home,
			'standard' => $sb_standard,
			'alt-font' => $sb_alt_font
		)
	));
?>
