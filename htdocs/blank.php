<?php
	include 'debug.php';

	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);

	require_once 'vendor/twig/twig/lib/Twig/Autoloader.php';
	Twig_Autoloader::register();

	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader);

	$template = $twig->loadTemplate('base.html');

	$variable = array(
		'title' => 'Booger');

	echo $template->render(array('test' => $variable));
?>
