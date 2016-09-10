<?php
include 'environment.php';
$cm = new Environment();

session_name($cm->session_name);
session_start();

$cm->enableDatabase($dal, $do2db);

$site_user = NULL;
$logged_in = false;

if (isset($_SESSION['uid'])) {

	$logged_in = true;

	// check if user is registered
	// if so, get user info
	$site_user = \dobj\User::createFromId($_SESSION['uid'], $dal, $do2db)->prepare($cm);
}

$cm->closeConnection();

// base layout
$base = file_get_contents($cm->template_dir . $cm->ds . 'base.html');

// get engine
$m = new Mustache_Engine(array(
  'pragmas' => array(Mustache_Engine::PRAGMA_BLOCKS),
  'partials' => array(
    'layout' => $base
  ),
));


// get actual site
$template = file_get_contents(__DIR__.$cm->ds.'templates'.$cm->ds.'404.html');
$page_vars = array(
	'title' => '404 File Not Found',
	'vars' => $cm->getVars(),
	'logged_in' => $logged_in,
	'site_user' => $site_user
);

echo $m->render($template, $page_vars);

?>
