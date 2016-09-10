<?php
include("../environment.php");
$cm = new Environment();

// base layout
$base = file_get_contents($cm->template_dir . $cm->ds . 'base.html');

// get engine
$m = new Mustache_Engine(array(
  'pragmas' => array(Mustache_Engine::PRAGMA_BLOCKS),
  'partials' => array(
    'layout' => $base
  ),
));

if (isset($_SESSION['uid']))
	$logged_in = true;
else
	$logged_in = false;

// mustache components
$m_comp = new \misc\MustacheComponent();

$searchbar_template = file_get_contents($cm->template_dir . $cm->ds . 'searchbar.html');
$sb_alt_font = $m_comp->render($searchbar_template, array('alt-font' => True, 'alt-color' => True, 'vars'=>$cm->getVars()));

// get actual site
$template = file_get_contents(__DIR__.$cm->ds.'templates'.$cm->ds.'postfileupload.html');
$page_vars = array(
	'vars' => $cm->getVars(),
	'logged_in' => $logged_in,
	'searchbars' => array(
		'alt-font' => $sb_alt_font
	)
);

echo $m->render($template, $page_vars);
?>
