<?php
include("../environment.php");
$cm = new \Environment();

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

// get actual site
$template = file_get_contents(__DIR__.$cm->ds.'templates'.$cm->ds.'twitterquery.html');
$page_vars = array(
	'vars' => $cm->getVars(),
	'logged_in' => $logged_in,
);

echo $m->render($template, $page_vars);
?>
